<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['replies', 'assignedTo'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $tickets = $query->paginate(15);

        return Inertia::render('User/Support/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('User/Support/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'category' => 'required|in:technical,billing,general,service_inquiry,complaint',
            'priority' => 'required|in:low,normal,high,urgent',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-tickets', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'attachments' => $attachments,
            'status' => 'open',
        ]);

        return redirect()->route('support.show', $ticket->id)
            ->with('success', __('Support ticket created successfully. Ticket #:number', ['number' => $ticket->ticket_number]));
    }

    public function show(SupportTicket $ticket)
    {
        // Ensure user can only view their own tickets
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['replies.user', 'assignedTo']);

        return Inertia::render('User/Support/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        // Ensure user can only reply to their own tickets
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-tickets', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'attachments' => $attachments,
            'is_staff_reply' => false,
        ]);

        // Update ticket status to awaiting_reply if it was resolved
        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'awaiting_reply']);
        }

        return back()->with('success', __('Reply added successfully'));
    }

    public function close(SupportTicket $ticket)
    {
        // Ensure user can only close their own tickets
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', __('Ticket closed successfully'));
    }

    public function rate(Request $request, SupportTicket $ticket)
    {
        // Ensure user can only rate their own tickets
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'satisfaction_rating' => $validated['rating'],
            'satisfaction_feedback' => $validated['feedback'] ?? null,
        ]);

        return back()->with('success', __('Thank you for your feedback!'));
    }
}
