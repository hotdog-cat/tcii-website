@extends('layouts.admin')
@section('title', 'Contact Messages')
@section('content')
<div class="page-heading"><div><p class="kicker">Inbox</p><h1>Contact messages</h1><p>Inquiries submitted through the public website.</p></div></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Status</th><th>From</th><th>Subject</th><th>Phone</th><th>Received</th><th>Actions</th></tr></thead><tbody>
@forelse($messages as $message)<tr><td><span class="status {{ $message->is_read ? '' : 'unread' }}">{{ $message->is_read ? 'Read' : 'New' }}</span></td><td><strong>{{ $message->name }}</strong><small>{{ $message->email }}</small></td><td>{{ $message->subject ?: 'Website inquiry' }}</td><td>{{ $message->phone ?: '—' }}</td><td>{{ $message->created_at->format('M d, Y g:i A') }}</td><td><div class="actions"><a href="{{ route('admin.messages.show',$message) }}">Open</a><form method="post" action="{{ route('admin.messages.destroy',$message) }}" onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')<button>Delete</button></form></div></td></tr>
@empty<tr><td colspan="6">No messages yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $messages->links() }}</div></section>
@endsection
