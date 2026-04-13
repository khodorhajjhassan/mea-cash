<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::query()->orderBy('method')->get();

        return view('admin.payment-methods.index', compact('methods'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'display_name_en' => ['required', 'string', 'max:255'],
            'display_name_ar' => ['required', 'string', 'max:255'],
            'account_identifier' => ['required', 'string', 'max:255'],
            'instructions_en' => ['nullable', 'string'],
            'instructions_ar' => ['nullable', 'string'],
        ]);

        try {
            $paymentMethod->update($data);

            return back()->with('success', 'Payment method updated.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to update payment method.');
        }
    }

    public function toggle(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->update(['is_active' => ! $paymentMethod->is_active]);

            return back()->with('success', 'Payment method status updated.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to toggle payment method.');
        }
    }
}
