<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('contact_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('is_active', $request->string('status')->value() === 'active');
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'status']);

        return view('admin.suppliers.index', compact('suppliers', 'filters'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            Supplier::query()->create($data);

            return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create supplier.');
        }
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $supplier->update($data);

            return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to update supplier.');
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();

            return back()->with('success', 'Supplier deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete supplier.');
        }
    }
}
