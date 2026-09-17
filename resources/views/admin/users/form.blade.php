@extends('layouts.admin')
@section('title', $user->exists ? 'Edit User' : 'Add User')
@section('content')
<div class="page-heading"><div><p class="kicker">Administration</p><h1>{{ $user->exists ? 'Edit user' : 'Add user' }}</h1><p>Set the profile, contact information, and CMS role.</p></div></div>
<form class="panel form-panel user-form-panel" method="post" enctype="multipart/form-data" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if($user->exists) @method('PUT') @endif
    <div class="form-grid user-form-grid">
        <label>Full Name<input name="name" value="{{ old('name', $user->name) }}" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
        <label>Contact Number<input name="contact_number" value="{{ old('contact_number', $user->contact_number) }}"></label>
        <label>Role<select name="role" required>@foreach(\App\Models\User::ROLES as $key => $label)<option value="{{ $key }}" @selected(old('role', $user->role) === $key)>{{ $label }}</option>@endforeach</select><small>Editors can edit content but cannot delete or open user management and activity logs.</small></label>
        <label>Password<input type="password" name="password" {{ $user->exists ? '' : 'required' }} autocomplete="new-password"><small>{{ $user->exists ? 'Leave blank to keep the current password.' : 'Minimum 8 characters.' }}</small></label>
        <label>Confirm Password<input type="password" name="password_confirmation" {{ $user->exists ? '' : 'required' }} autocomplete="new-password"></label>
        <div class="image-field profile-upload-field">
            <label>Profile Photo<input type="file" name="profile_photo" accept="image/*"><small>JPG, PNG, or WebP up to 5 MB.</small></label>
            @if($user->profile_photo_url)<div class="current-image profile-current"><img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"><label class="check"><input type="checkbox" name="remove_photo" value="1"> Remove current photo</label></div>@endif
        </div>
    </div>
    <div class="form-actions"><a href="{{ route('admin.users.index') }}">Cancel</a><button class="primary-btn" type="submit">{{ $user->exists ? 'Save User' : 'Create User' }}</button></div>
</form>
@endsection
