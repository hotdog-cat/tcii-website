<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $items = ContentItem::query()
            ->when(array_key_exists($type, ContentItem::TYPES), fn ($query) => $query->where('type', $type))
            ->orderBy('type')->orderBy('sort_order')->paginate(15)->withQueryString();

        return view('admin.items.index', compact('items', 'type'));
    }

    public function create(Request $request): View
    {
        $type = $request->string('type')->toString();
        abort_unless(array_key_exists($type, ContentItem::TYPES), 404);

        $item = new ContentItem(['type' => $type, 'is_published' => true]);
        return view('admin.items.form', compact('item'));
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $request->query('type');
        abort_unless(is_string($type) && array_key_exists($type, ContentItem::TYPES), 404);

        $data = $this->validated($request, $type);
        $data['type'] = $type;
        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('content', 'public');
        $item = ContentItem::create($data);
        ActivityLog::record('content.created', "Created {$item->title}.", $item);

        return redirect()->route('admin.items.index', ['type' => $data['type']])->with('success', 'Content item created.');
    }

    public function edit(ContentItem $item): View
    {
        return view('admin.items.form', compact('item'));
    }

    public function update(Request $request, ContentItem $item): RedirectResponse
    {
        if ($request->boolean('remove_image')) {
            $this->deleteStoredImage($item);
            $item->update(['image_path' => null]);

            return redirect()->route('admin.items.edit', $item)->with('success', 'Image removed.');
        }

        $data = $this->validated($request, $item->type);
        $data['type'] = $item->type;
        if ($request->hasFile('image')) {
            $this->deleteStoredImage($item);
            $data['image_path'] = $request->file('image')->store('content', 'public');
        }
        $item->update($data);
        ActivityLog::record('content.updated', "Updated {$item->title}.", $item);

        return redirect()->route('admin.items.index', ['type' => $data['type']])->with('success', 'Content item updated.');
    }

    public function publication(Request $request, ContentItem $item): RedirectResponse
    {
        $data = $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $item->update(['is_published' => (bool) $data['is_published']]);
        ActivityLog::record('content.publication', ($item->is_published ? 'Published ' : 'Saved draft for ').$item->title.'.', $item);

        return redirect()
            ->route('admin.items.index', ['type' => $item->type])
            ->with('success', $item->is_published ? 'Content item published.' : 'Content item saved as draft.');
    }

    public function destroy(ContentItem $item): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $this->deleteStoredImage($item);
        $type = $item->type;
        $title = $item->title;
        $item->delete();
        ActivityLog::record('content.deleted', "Deleted {$title}.");

        return redirect()->route('admin.items.index', ['type' => $type])->with('success', 'Content item deleted.');
    }

    private function validated(Request $request, string $type): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'subtitle' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:3000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        if (! in_array($type, ['product', 'team'], true)) {
            $data['description'] = null;
        }
        unset($data['image']);
        return $data;
    }

    private function deleteStoredImage(ContentItem $item): void
    {
        if ($item->image_path && ! str_starts_with($item->image_path, 'images/')) {
            Storage::disk('public')->delete($item->image_path);
        }
    }
}
