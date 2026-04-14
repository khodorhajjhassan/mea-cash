<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly WalletService $walletService)
    {
    }

    public function index(Request $request)
    {
        $users = User::query()
            ->with('wallet')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
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

        return view('admin.users.index', compact('users', 'filters'));
    }

    public function show(User $user)
    {
        $user->load(['wallet', 'orders' => function($q) {
            $q->latest()->limit(10);
        }, 'wallet.transactions' => function($q) {
            $q->with('processor')->latest()->limit(10);
        }]);

        // Calculate total spent on completed orders
        $totalSpent = \App\Models\Order::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_price');

        return view('admin.users.show', compact('user', 'totalSpent'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'preferred_language' => ['required', 'in:ar,en'],
            'is_active' => ['required', 'boolean'],
            'is_admin' => ['required', 'boolean'],
        ]);

        try {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            $user = User::query()->create($data);

            return redirect()->route('admin.users.show', $user)->with('success', 'User created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'preferred_language' => ['required', 'in:ar,en'],
            'is_active' => ['required', 'boolean'],
            'is_admin' => ['required', 'boolean'],
        ]);

        try {
            if (!empty($data['password'])) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to update user.');
        }
    }

    public function credit(Request $request, User $user)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->walletService->credit($user, (float) $data['amount'], $data['description'], null, auth()->id());

            return back()->with('success', 'Wallet credited successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to credit wallet: ' . $exception->getMessage());
        }
    }

    public function vip()
    {
        $users = User::query()
            ->with('orders:id,user_id,total_price,status')
            ->get()
            ->sortByDesc(fn (User $user) => $user->orders->where('status', 'completed')->sum('total_price'))
            ->take(50);

        return view('admin.users.vip', compact('users'));
    }
}

