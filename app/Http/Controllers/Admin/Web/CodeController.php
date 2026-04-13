<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCode;
use Exception;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function index(Request $request)
    {
        $codes = ProductCode::query()
            ->with(['product:id,name_en', 'package:id,name_en'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('notes', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name_en', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->value()))
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', (int) $request->integer('product_id')))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'status', 'product_id']);

        return view('admin.codes.index', compact('codes', 'filters'));
    }

    public function import()
    {
        $products = Product::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en']);

        return view('admin.codes.import', compact('products'));
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        try {
            $rows = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));

            foreach ($rows as $row) {
                if (!isset($row[0]) || trim($row[0]) === '') {
                    continue;
                }

                ProductCode::query()->create([
                    'product_id' => (int) $request->input('product_id'),
                    'code' => trim($row[0]),
                    'notes' => $row[1] ?? null,
                    'status' => 'available',
                ]);
            }

            return redirect()->route('admin.codes.index')->with('success', 'Codes imported successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to import CSV codes.');
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'package_id' => ['nullable', 'exists:product_packages,id'],
            'code' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            ProductCode::query()->create($data + ['status' => 'available']);

            return back()->with('success', 'Code added successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to add code.');
        }
    }

    public function destroy(ProductCode $code)
    {
        try {
            $code->delete();

            return back()->with('success', 'Code deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete code.');
        }
    }
}
