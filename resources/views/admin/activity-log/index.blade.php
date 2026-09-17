@extends('layouts.admin')
@section('title', 'Activity Log')
@section('content')
<div class="page-heading"><div><p class="kicker">Administration</p><h1>System activity log</h1><p>Review recent CMS sign-ins, content changes, and account updates.</p></div></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Date</th><th>User</th><th>Action</th><th>Activity</th><th>IP Address</th></tr></thead><tbody>
@forelse($logs as $log)<tr><td>{{ $log->created_at->format('M d, Y g:i A') }}</td><td>@if($log->user)<strong>{{ $log->user->name }}</strong><small>{{ $log->user->email }}</small>@else<small>System or deleted user</small>@endif</td><td><span class="status">{{ $log->action }}</span></td><td>{{ $log->description }}</td><td>{{ $log->ip_address ?: 'Not captured' }}</td></tr>
@empty<tr><td colspan="5">No activity has been recorded yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $logs->links() }}</div></section>
@endsection
