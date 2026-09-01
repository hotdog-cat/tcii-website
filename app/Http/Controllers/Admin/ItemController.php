<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
        $item = new ContentItem(['type' => $request->string('type')->toString(), 'is_published' => true]);
        return view('admin.items.form', compact('item'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('content', 'public');
        ContentItem::create($data);
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

        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $this->deleteStoredImage($item);
            $data['image_path'] = $request->file('image')->store('content', 'public');
        }
        $item->update($data);
        return redirect()->route('admin.items.index', ['type' => $data['type']])->with('success', 'Content item updated.');
    }

    public function destroy(ContentItem $item): RedirectResponse
    {
        $this->deleteStoredImage($item);
        $type = $item->type;
        $item->delete();
        return redirect()->route('admin.items.index', ['type' => $type])->with('success', 'Content item deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(ContentItem::TYPES))],
            'title' => ['required', 'string', 'max:190'],
            'subtitle' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:3000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
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
