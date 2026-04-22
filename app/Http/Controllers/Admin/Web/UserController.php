<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateUserRequest;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

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
                $query->where('is_active', $request->string('status')->value === 'active');
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
            ->where('status', OrderStatus::Completed)
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

    public function store(UpdateUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            
            $user = new User();
            $user->fill($data);
            $user->is_admin = $request->boolean('is_admin');
            $user->save();

            return redirect()->route('admin.users.show', $user)->with('success', 'User created successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->fill($data);
            $user->is_admin = $request->boolean('is_admin');
            $user->save();

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
            $this->walletService->credit($user, (string) $data['amount'], $data['description'], null, auth()->id());

            return back()->with('success', 'Wallet credited successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to credit wallet: ' . $exception->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ((int) $user->id === (int) auth()->id()) {
            return back()->with('error', 'You cannot delete your own account while you are logged in.');
        }

        try {
            DB::transaction(function () use ($user): void {
                $user->delete();
            });

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete user. Please review linked records and try again.');
        }
    }

    public function vip()
    {
        $users = User::query()
            ->with(['orders' => function($q) {
                $q->where('status', OrderStatus::Completed);
            }])
            ->get()
            ->sortByDesc(fn (User $user) => $user->orders->sum('total_price'))
            ->take(50);

        return view('admin.users.vip', compact('users'));
    }
}

