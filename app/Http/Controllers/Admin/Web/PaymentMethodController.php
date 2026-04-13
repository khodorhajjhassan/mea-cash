<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $methods = PaymentMethod::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($q): void {
                    $inner->where('method', 'like', "%{$q}%")
                        ->orWhere('display_name_en', 'like', "%{$q}%")
                        ->orWhere('display_name_ar', 'like', "%{$q}%")
                        ->orWhere('account_identifier', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('is_active', $request->string('status')->value() === 'active');
            })
            ->orderBy('method')
            ->get();

        $filters = $request->only(['q', 'status']);

        return view('admin.payment-methods.index', compact('methods', 'filters'));
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
