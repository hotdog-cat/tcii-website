<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PageContentController extends Controller
{
    public function edit(): View
    {
        return view('admin.page-content.edit', ['content' => PageContent::values()]);
    }

    public function update(Request $request): RedirectResponse
    {
        if ($request->filled('remove_media')) {
            $key = $request->string('remove_media')->toString();

            if (! in_array($key, ['hero_background_image', 'header_logo_image', 'footer_logo_image'], true)) {
                abort(404);
            }

            $currentValue = PageContent::values()[$key] ?? null;
            $this->deleteStoredMedia($currentValue);

            PageContent::updateOrCreate(['key' => $key], ['value' => PageContent::DEFAULTS[$key]]);
            ActivityLog::record('page.media_removed', "Removed {$key} and restored the default.");

            return back()->with('success', 'Image removed.');
        }

        $rules = [];
        $longFieldFragments = ['paragraph', 'description', 'intro', 'copy', 'address', 'telephones', 'mobiles', 'maintenance_message'];
        $nullableContentKeys = ['hero_background_image', 'contact_telephones'];

        foreach (PageContent::DEFAULTS as $key => $default) {
            if (str_starts_with($key, 'section_') || $key === 'maintenance_enabled') {
                $rules[$key] = ['required', 'boolean'];
                continue;
            }

            $isLongField = collect($longFieldFragments)->contains(fn (string $fragment) => str_contains($key, $fragment));
            $rules[$key] = [in_array($key, $nullableContentKeys, true) ? 'nullable' : 'required', 'string', $isLongField
                ? 'max:3000'
                : 'max:190'];
        }

        $rules['contact_email'][] = 'email';
        $rules['header_logo_width'] = ['required', 'integer', 'min:100', 'max:400'];
        $rules['footer_logo_width'] = ['required', 'integer', 'min:100', 'max:400'];
        $rules['hero_background_upload'] = ['nullable', 'image', 'max:10240'];
        $rules['header_logo_upload'] = ['nullable', 'image', 'max:10240'];
        $rules['footer_logo_upload'] = ['nullable', 'image', 'max:10240'];

        $validated = $request->validate($rules);
        $contentValues = array_intersect_key($validated, PageContent::DEFAULTS);
        foreach ($nullableContentKeys as $key) {
            $contentValues[$key] = $contentValues[$key] ?? '';
        }

        foreach ([
            'hero_background_upload' => 'hero_background_image',
            'header_logo_upload' => 'header_logo_image',
            'footer_logo_upload' => 'footer_logo_image',
        ] as $uploadKey => $contentKey) {
            if ($request->hasFile($uploadKey)) {
                $this->deleteStoredMedia(PageContent::values()[$contentKey] ?? null);
                $storedPath = $request->file($uploadKey)->store('page-content', 'public');
                $contentValues[$contentKey] = Storage::disk('public')->url($storedPath);
            }
        }

        DB::transaction(function () use ($contentValues): void {
            foreach ($contentValues as $key => $value) {
                PageContent::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        });

        ActivityLog::record('page.updated', 'Updated page content and media settings.');

        return back()->with('success', 'Page content updated.');
    }

    private function deleteStoredMedia(?string $value): void
    {
        if (! $value || ! str_starts_with($value, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $value));
    }
}
