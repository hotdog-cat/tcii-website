@extends('layouts.admin')
@section('title', $type && isset(\App\Models\ContentItem::TYPES[$type]) ? \App\Models\ContentItem::TYPES[$type] : 'All Content')
@section('content')
<div class="page-heading"><div><p class="kicker">Website Content</p><h1>{{ $type && isset(\App\Models\ContentItem::TYPES[$type]) ? \App\Models\ContentItem::TYPES[$type] : 'All Content' }}</h1><p>Edit, publish, reorder, or add website records.</p></div><a class="primary-btn" href="{{ route('admin.items.create', ['type' => $type]) }}">+ Add Item</a></div>
<section class="panel">
<div class="table-wrap"><table><thead><tr><th>Order</th><th>Content</th><th>Type</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead><tbody>
@forelse($items as $item)<tr><td>{{ $item->sort_order }}</td><td><div class="content-cell">@if($item->image_url)<img src="{{ $item->image_url }}" alt="">@endif<div><strong>{{ $item->title }}</strong><small>{{ $item->subtitle }}</small></div></div></td><td>{{ \App\Models\ContentItem::TYPES[$item->type] }}</td><td><span class="status {{ $item->is_published ? 'published' : 'draft' }}">{{ $item->is_published ? 'Published' : 'Draft' }}</span></td><td>{{ $item->updated_at->format('M d, Y') }}</td><td><div class="actions"><form class="publish-action" method="post" action="{{ route('admin.items.publication', $item) }}">@csrf @method('PATCH')<label><span class="sr-only">Publication status</span><select name="is_published" onchange="this.form.submit()"><option value="1" @selected($item->is_published)>Publish</option><option value="0" @selected(! $item->is_published)>Draft</option></select></label></form><a href="{{ route('admin.items.edit', $item) }}">Edit</a>@if(auth()->user()?->isAdmin())<form method="post" action="{{ route('admin.items.destroy', $item) }}" onsubmit="return confirm('Delete this item?')">@csrf @method('DELETE')<button type="submit">Delete</button></form>@endif</div></td></tr>
@empty<tr><td colspan="6">No content items found.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $items->links() }}</div>
</section>
@endsection
