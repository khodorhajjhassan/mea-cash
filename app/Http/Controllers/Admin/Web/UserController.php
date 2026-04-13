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

    public function index()
    {
        $users = User::query()->with('wallet')->latest('id')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('wallet');

        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'preferred_language' => ['required', 'in:ar,en'],
        ]);

        try {
            $user->update($data);

            return back()->with('success', 'User updated successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to update user.');
        }
    }

    public function toggle(User $user)
    {
        try {
            $user->update(['is_active' => ! $user->is_active]);

            return back()->with('success', 'User status updated.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to toggle user status.');
        }
    }

    public function credit(Request $request, User $user)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->walletService->credit($user, (float) $data['amount'], $data['description']);

            return back()->with('success', 'Wallet credited successfully.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to credit wallet.');
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
