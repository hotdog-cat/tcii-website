<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\ContentItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'itemCounts' => ContentItem::selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type'),
            'unreadMessages' => ContactMessage::where('is_read', false)->count(),
            'latestMessages' => ContactMessage::latest()->limit(5)->get(),
        ]);
    }
}
