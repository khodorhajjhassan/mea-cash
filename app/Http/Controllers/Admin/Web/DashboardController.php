<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\TopupRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'categories' => Category::query()->count(),
            'subcategories' => Subcategory::query()->count(),
            'products' => Product::query()->count(),
            'orders' => Order::query()->count(),
            'pending_topups' => TopupRequest::query()->where('status', 'pending')->count(),
            'users' => User::query()->count(),
        ];

        $latestProducts = Product::query()
            ->with('subcategory:id,name_en')
            ->latest('id')
            ->limit(8)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'latestProducts'));
    }

    public function __invoke()
    {
        return $this->index();
    }
}
