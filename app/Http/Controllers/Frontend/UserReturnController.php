<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ReturnStoreRequest;
use App\Mail\ReturnRequestCustomerMail;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use App\Notifications\NewReturnNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = OrderReturn::with('order')
            ->whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($returns);
        }

        return view('pages.user-panel.returns', compact('returns'));
    }

    public function store(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);
        
        if ($order->status !== 'delivered') {
            return back()->with('error', 'Return allowed only for delivered orders.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:' . implode(',', \App\Support\OrderFlow::RETURN_REASONS)],
            'comments' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($order->returns()->whereIn('status', ['pending', 'approved', 'completed'])->exists()) {
            return back()->with('error', 'Return request is already raised for this order.');
        }

        $orderReturn = OrderReturn::create([
            'order_id' => $order->id,
            'return_number' => 'RET-' . now()->format('Ymd') . strtoupper(Str::random(5)),
            'reason' => $validated['reason'] . ($validated['comments'] ? ': ' . $validated['comments'] : ''),
            'status' => 'pending',
            'refund_amount' => 0,
        ]);

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
