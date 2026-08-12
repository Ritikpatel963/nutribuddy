<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ReturnStoreRequest;
use App\Mail\ReturnRequestCustomerMail;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\User;
use App\Notifications\NewReturnNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = OrderReturn::with(['order', 'items.orderItem'])
            ->whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($returns);
        }

        return view('pages.user-panel.returns', compact('returns'));
    }

    public function store(ReturnStoreRequest $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);
        
        if ($order->status !== 'delivered') {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Return allowed only for delivered orders.',
                ], 422);
            }

            return back()->with('error', 'Return allowed only for delivered orders.');
        }

        $validated = $request->validated();

        $order->load(['items.returnItems.returnRequest']);
        $returnLines = collect($validated['items'])
            ->mapWithKeys(fn ($line) => [(int) $line['order_item_id'] => (int) $line['quantity']]);

        $orderItemsById = $order->items->keyBy('id');
        $invalidLines = [];
        $lineModels = [];

        foreach ($returnLines as $orderItemId => $quantity) {
            $item = $orderItemsById->get($orderItemId);
            if (! $item) {
                $invalidLines[] = 'Invalid item selected.';
                continue;
            }

            $alreadyRequested = $item->returnItems
                ->filter(fn ($returnItem) => in_array($returnItem->returnRequest?->status, ['pending', 'approved', 'completed'], true))
                ->sum('quantity');

            $available = max(0, (int) $item->quantity - (int) $alreadyRequested);
            if ($quantity > $available) {
                $invalidLines[] = "{$item->product_name} has only {$available} returnable quantity left.";
                continue;
            }

            $lineModels[] = [$item, $quantity];
        }

        if ($invalidLines || empty($lineModels)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $invalidLines[0] ?? 'Please select at least one return item.',
                    'errors' => ['items' => $invalidLines ?: ['Please select at least one return item.']],
                ], 422);
            }

            return back()->with('error', $invalidLines[0] ?? 'Please select at least one return item.');
        }

        $mediaPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('returns', 'public');
                $mediaPaths[] = $path;
            }
        }

        $orderReturn = DB::transaction(function () use ($order, $validated, $mediaPaths, $lineModels) {
            $orderReturn = OrderReturn::create([
                'order_id' => $order->id,
                'return_number' => 'RET-' . now()->format('Ymd') . strtoupper(Str::random(5)),
                'reason' => $validated['reason'] . (!empty($validated['comments']) ? ': ' . $validated['comments'] : ''),
                'status' => 'pending',
                'refund_amount' => collect($lineModels)->sum(
                    fn ($line) => round(((float) $line[0]->line_total / max(1, (int) $line[0]->quantity)) * (int) $line[1], 2)
                ),
                'media_paths' => count($mediaPaths) > 0 ? $mediaPaths : null,
            ]);

            foreach ($lineModels as [$item, $quantity]) {
                $unitRefund = (float) $item->line_total / max(1, (int) $item->quantity);
                OrderReturnItem::create([
                    'order_return_id' => $orderReturn->id,
                    'order_item_id' => $item->id,
                    'product_name' => $item->product_name,
                    'sku' => $item->sku,
                    'quantity' => $quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => round($unitRefund * $quantity, 2),
                ]);
            }

            return $orderReturn->load('items.orderItem');
        });

        // Email to customer
        if ($request->user()->email) {
            try {
                Mail::to($request->user()->email)->queue(new ReturnRequestCustomerMail($orderReturn));
            } catch (\Exception $e) {}
        }

        // Notify admins
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewReturnNotification($orderReturn->load('order')));
            }
        } catch (\Exception $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Return request submitted successfully.',
                'return' => $orderReturn,
            ], 201);
        }

        return redirect()->route('user.orders.detail-page', $order)->with('success', 'Return request submitted successfully.');
    }
}
