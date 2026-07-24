@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading"><div><p class="kicker">Overview</p><h1>Website dashboard</h1><p>Manage public content and review customer inquiries.</p></div></div>
<div class="stat-grid">
    @foreach(\App\Models\ContentItem::TYPES as $key => $label)
    <a class="stat-card" href="{{ route('admin.items.index', ['type' => $key]) }}"><span>{{ $label }}</span><strong>{{ $itemCounts[$key] ?? 0 }}</strong><small>Manage content →</small></a>
    @endforeach
    <a class="stat-card accent" href="{{ route('admin.messages.index') }}"><span>Unread Messages</span><strong>{{ $unreadMessages }}</strong><small>Open inbox →</small></a>
</div>
<section class="panel"><div class="panel-title"><div><p class="kicker">Inbox</p><h2>Recent inquiries</h2></div><a href="{{ route('admin.messages.index') }}">View all</a></div>
    <div class="table-wrap"><table><thead><tr><th>Status</th><th>From</th><th>Subject</th><th>Received</th><th></th></tr></thead><tbody>
    @forelse($latestMessages as $message)<tr><td><span class="status {{ $message->is_read ? '' : 'unread' }}">{{ $message->is_read ? 'Read' : 'New' }}</span></td><td><strong>{{ $message->name }}</strong><small>{{ $message->email }}</small></td><td>{{ $message->subject ?: 'Website inquiry' }}</td><td>{{ $message->created_at->format('M d, Y g:i A') }}</td><td><a href="{{ route('admin.messages.show', $message) }}">Open</a></td></tr>@empty<tr><td colspan="5">No inquiries yet.</td></tr>@endforelse
    </tbody></table></div>
</section>
@endsection
