<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\User;
use App\Support\AdminPermissions;
use App\Support\PasswordPolicy;
use Illuminate\Http\Request;

/**
 * Admin Management — the Main Admin creates/edits Sub Admin accounts and
 * their permissions here. Scoped ONLY to role='sub' users: there is no
 * field or route path anywhere in this controller that writes role='main',
 * so a Sub Admin can never be created or promoted through it, and Main
 * Admin accounts are never touched by it (see EnsureUserIsMainAdmin for the
 * route-level guard keeping Sub Admins out of this controller entirely).
 */
class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'admins' => User::where('role', 'sub')->orderBy('email')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => PasswordPolicy::rules(),
        ]);

        $user = User::create([
            'name' => $data['email'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => true,
            'role' => 'sub',
            'permissions' => $this->sanitizePermissions($request->input('permissions', [])),
        ]);

        AdminActivityLog::record('sub_admin_created', $user->email);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('Sub Admin created successfully.'));
    }

    public function edit(User $user)
    {
        abort_unless($user->role === 'sub', 404);

        return view('admin.users.edit', ['admin' => $user]);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'sub', 404);

        $user->update([
            'permissions' => $this->sanitizePermissions($request->input('permissions', [])),
        ]);

        AdminActivityLog::record('sub_admin_permissions_updated', $user->email);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('Permissions saved.'));
    }

    public function showPasswordForm(User $user)
    {
        abort_unless($user->role === 'sub', 404);

        return view('admin.users.password', ['admin' => $user]);
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_unless($user->role === 'sub', 404);

        $data = $request->validate([
            'password' => PasswordPolicy::rules(),
        ]);

        $user->update(['password' => $data['password']]);

        AdminActivityLog::record('sub_admin_password_reset', $user->email);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('Password updated.'));
    }

    public function toggle(User $user)
    {
        abort_unless($user->role === 'sub', 404);

        $user->update(['disabled_at' => $user->isDisabled() ? null : now()]);

        AdminActivityLog::record($user->isDisabled() ? 'sub_admin_disabled' : 'sub_admin_enabled', $user->email);

        return redirect()
            ->route('admin.users.index')
            ->with('success', $user->isDisabled() ? __('Sub Admin disabled.') : __('Sub Admin enabled.'));
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'sub', 404);

        $email = $user->email;
        $user->delete();

        AdminActivityLog::record('sub_admin_deleted', $email);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('Sub Admin removed.'));
    }

    /**
     * Keeps only section/action pairs that actually exist in the central
     * registry — defense in depth beyond the basic "is this an array of
     * strings" request validation, since the raw request could name any
     * section/action key.
     */
    private function sanitizePermissions(array $raw): array
    {
        $sections = AdminPermissions::sections();
        $clean = [];

        foreach ($sections as $key => $definition) {
            $granted = array_values(array_intersect($definition['actions'], $raw[$key] ?? []));

            if ($granted) {
                $clean[$key] = $granted;
            }
        }

        return $clean;
    }
}
