<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\ContentItem;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $content = ContentItem::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('type');

        $contentJson = $content->map(fn ($items) => $items->map(fn (ContentItem $item) => [
            'id' => $item->id,
            'title' => $item->title,
            'subtitle' => $item->subtitle,
            'description' => $item->description,
            'image' => $item->image_url,
            'sort_order' => $item->sort_order,
        ])->values());

        $pageContent = PageContent::values();

        return view('react', compact('contentJson', 'pageContent'));
    }

    public function contact(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:150'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($validated);
        $successMessage = PageContent::values()['inquiry_success_message'];

        if ($request->expectsJson()) {
            return response()->json(['message' => $successMessage], 201);
        }

        return back()->with('success', $successMessage);
    }
}
