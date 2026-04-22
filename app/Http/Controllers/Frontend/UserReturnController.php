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
    public function index(Request $request): JsonResponse
    {
        $returns = OrderReturn::with('order')
            ->whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(10);

        return response()->json($returns);
    }

    public function store(ReturnStoreRequest $request, Order $order): JsonResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);
        abort_unless(in_array($order->status, ['delivered'], true), 422, 'Return allowed only for delivered orders.');

        $validated = $request->validated();
        abort_if(
            $order->returns()->whereIn('status', ['pending', 'approved'])->exists(),
            422,
            'Return request is already raised for this order.'
        );

        $orderReturn = OrderReturn::create([
            'order_id' => $order->id,
            'return_number' => 'RET-' . now()->format('Ymd') . strtoupper(Str::random(5)),
            'reason' => $validated['reason'],
            'status' => 'pending',
            'refund_amount' => 0,
        ]);

        if ($request->user()->email) {
            Mail::to($request->user()->email)->queue(new ReturnRequestCustomerMail($orderReturn));
        }

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewReturnNotification($orderReturn->load('order')));
        }

        return response()->json([
            'message' => 'Return request submitted successfully.',
            'return' => $orderReturn,
        ], 201);
    }
}
