<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('admin.table', [
            'title' => 'Payments',
            'items' => Payment::with('order:id,order_number')->orderBy('id')->paginate(15),
            'columns' => [
                'ID' => fn ($m) => $m->id,
                'Order #' => fn ($m) => $m->order->order_number,
                'Reference' => fn ($m) => $m->payment_reference,
                'Method' => fn ($m) => $m->payment_method,
                'Amount' => fn ($m) => number_format($m->amount, 2),
                'Paid at' => fn ($m) => $m->paid_at,
                'Created' => fn ($m) => $m->created_at,
                'Updated' => fn ($m) => $m->updated_at,
            ],
        ]);
    }

    public function store(Request $request, Order $order, PaymentService $payments): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'gcash', 'bank_transfer'])],
        ]);

        $payments->record($order, $data['amount'], $data['payment_method']);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Payment recorded.');
    }
}
