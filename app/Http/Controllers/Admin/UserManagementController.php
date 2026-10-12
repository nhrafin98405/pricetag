<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Show the Admin account creation form.
     */
    public function create(): View
    {
        abort_unless(
            auth()->user()?->hasRole('Super Admin'),
            403,
            'Only the Super Admin can create Admin accounts.'
        );

        return view('admin.users.create');
    }

    /**
     * Create an Admin account.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(
            $request->user()?->hasRole('Super Admin'),
            403,
            'Only the Super Admin can create Admin accounts.'
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $adminRole = Role::query()
            ->where('name', 'Admin')
            ->where('guard_name', 'web')
            ->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($adminRole);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Admin account created successfully.');
    }
}