@extends('layouts.admin')
@section('title', 'User Management')
@section('content')
<div class="page-heading"><div><p class="kicker">Administration</p><h1>User management</h1><p>Manage CMS access, profile photos, and contact details.</p></div><a class="primary-btn" href="{{ route('admin.users.create') }}">+ Add User</a></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Profile</th><th>Role</th><th>Contact</th><th>Updated</th><th>Actions</th></tr></thead><tbody>
@forelse($users as $user)<tr><td><div class="user-cell">@if($user->profile_photo_url)<img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">@else<span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>@endif<div><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></div></div></td><td><span class="status {{ $user->isAdmin() ? 'published' : '' }}">{{ $user->role_label }}</span></td><td>{{ $user->contact_number ?: 'Not provided' }}</td><td>{{ $user->updated_at->format('M d, Y') }}</td><td><div class="actions"><a href="{{ route('admin.users.edit', $user) }}">Edit</a>@unless($user->is(auth()->user()))<form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user account?')">@csrf @method('DELETE')<button type="submit">Delete</button></form>@endunless</div></td></tr>
@empty<tr><td colspan="5">No user accounts found.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $users->links() }}</div></section>
@endsection
