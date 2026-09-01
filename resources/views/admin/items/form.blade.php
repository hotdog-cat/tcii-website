@extends('layouts.admin')
@section('title', $item->exists ? 'Edit Content' : 'Add Content')
@section('content')
<div class="page-heading"><div><p class="kicker">Website Content</p><h1>{{ $item->exists ? 'Edit content item' : 'Add content item' }}</h1><p>Changes affect the public website after saving.</p></div></div>
<form class="panel form-panel" method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.items.update', $item) : route('admin.items.store') }}">@csrf @if($item->exists)@method('PUT')@endif
    <div class="form-grid">
        <label>Content type<select name="type" required>@foreach(\App\Models\ContentItem::TYPES as $key=>$label)<option value="{{ $key }}" @selected(old('type',$item->type)===$key)>{{ $label }}</option>@endforeach</select></label>
        <label>Display order<input type="number" name="sort_order" min="0" value="{{ old('sort_order',$item->sort_order ?? 0) }}" required></label>
        <label class="wide">Title<input name="title" maxlength="190" value="{{ old('title',$item->title) }}" required></label>
        <label class="wide">Subtitle / role / location<input name="subtitle" maxlength="190" value="{{ old('subtitle',$item->subtitle) }}"></label>
        <label class="wide">Description<textarea name="description" rows="6">{{ old('description',$item->description) }}</textarea><small>For team board members, use <b>BOARD</b> in the description to place them in Owners &amp; Board.</small></label>
        <div class="wide image-field">
            <label>Image<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></label>
            @if($item->image_url)
                <span class="current-image"><img src="{{ $item->image_url }}" alt="">Current image <button class="danger-btn remove-image-btn" type="submit" name="remove_image" value="1" formnovalidate onclick="return confirm('Remove this image?')">Remove Image</button></span>
            @endif
        </div>
        <label class="check wide"><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$item->is_published ?? true))> Publish this item on the public website</label>
    </div>
    <div class="form-actions"><a href="{{ route('admin.items.index', ['type'=>$item->type]) }}">Cancel</a><button class="primary-btn" type="submit">Save Content</button></div>
</form>
@endsection
