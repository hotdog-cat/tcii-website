<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('admin.messages.index', ['messages' => ContactMessage::latest()->paginate(20)]);
    }
    public function show(ContactMessage $message): View
    {
        $message->update(['is_read' => true]);
        return view('admin.messages.show', compact('message'));
    }
    public function read(ContactMessage $message): RedirectResponse
    {
        $message->update(['is_read' => true]);
        return back()->with('success', 'Message marked as read.');
    }
    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
