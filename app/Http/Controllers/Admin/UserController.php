<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(['role' => User::ROLE_EDITOR]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_admin'] = $data['role'] === User::ROLE_ADMIN;

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user = User::create($data);

        ActivityLog::record('user.created', "Created {$user->role_label} account for {$user->name}.", $user);

        return redirect()->route('admin.users.index')->with('success', 'User account created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        $data['is_admin'] = $data['role'] === User::ROLE_ADMIN;

        if (! $data['is_admin'] && $user->is(auth()->user()) && User::query()->where('role', User::ROLE_ADMIN)->whereKeyNot($user->id)->doesntExist()) {
            return back()->withErrors(['role' => 'At least one Admin account is required.'])->withInput();
        }

        if ($request->boolean('remove_photo')) {
            $this->deleteProfilePhoto($user);
            $data['profile_photo_path'] = null;
        }

        if ($request->hasFile('profile_photo')) {
            $this->deleteProfilePhoto($user);
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($data);

        ActivityLog::record('user.updated', "Updated account details for {$user->name}.", $user);

        return redirect()->route('admin.users.index')->with('success', 'User account updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 403);
        abort_if(User::query()->where('role', User::ROLE_ADMIN)->whereKeyNot($user->id)->doesntExist(), 403);

        $name = $user->name;
        $this->deleteProfilePhoto($user);
        $user->delete();

        ActivityLog::record('user.deleted', "Deleted account for {$name}.");

        return redirect()->route('admin.users.index')->with('success', 'User account deleted.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $passwordRules = $user?->exists ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user)],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => $passwordRules,
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        unset($data['profile_photo'], $data['remove_photo']);

        return $data;
    }

    private function deleteProfilePhoto(User $user): void
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
    }
}
