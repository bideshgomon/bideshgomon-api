<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\MarketingCampaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MarketingCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MarketingCampaign::with(['emailTemplate', 'creator']);

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('slug', 'like', '%'.$request->search.'%');
            });
        }

        $campaigns = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Calculate stats
        $stats = [
            'total' => MarketingCampaign::count(),
            'total_recipients' => MarketingCampaign::sum('total_recipients'),
            'avg_open_rate' => MarketingCampaign::where('sent_count', '>', 0)->avg('open_rate') ?? 0,
            'avg_click_rate' => MarketingCampaign::where('sent_count', '>', 0)->avg('click_rate') ?? 0,
        ];

        return Inertia::render('Admin/MarketingCampaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['type', 'status', 'search']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $emailTemplates = EmailTemplate::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'category']);

        return Inertia::render('Admin/MarketingCampaigns/Create', [
            'emailTemplates' => $emailTemplates,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms,push,notification',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'audience_type' => 'required|in:all_users,segment,custom',
            'audience_filters' => 'nullable|array',
            'recipient_ids' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'is_ab_test' => 'boolean',
            'ab_test_config' => 'nullable|array',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (MarketingCampaign::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug.'-'.$counter++;
        }

        // Set status
        $validated['status'] = isset($validated['scheduled_at']) ? 'scheduled' : 'draft';

        // Set creator
        $validated['created_by'] = auth()->id();

        // Calculate total recipients
        if ($validated['audience_type'] === 'all_users') {
            $validated['total_recipients'] = User::count();
        } elseif ($validated['audience_type'] === 'custom' && isset($validated['recipient_ids'])) {
            $validated['total_recipients'] = count($validated['recipient_ids']);
        } else {
            $validated['total_recipients'] = 0; // Will be calculated based on filters
        }

        $campaign = MarketingCampaign::create($validated);

        return redirect()->route('admin.marketing-campaigns.show', $campaign->id)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->load(['emailTemplate', 'creator']);

        return Inertia::render('Admin/MarketingCampaigns/Show', [
            'campaign' => $marketingCampaign,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingCampaign $marketingCampaign)
    {
        // Only allow editing draft or scheduled campaigns
        if (! in_array($marketingCampaign->status, ['draft', 'scheduled', 'paused'])) {
            return back()->withErrors([
                'message' => 'Cannot edit a campaign that is sending, sent, or cancelled.',
            ]);
        }

        $emailTemplates = EmailTemplate::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'category']);

        return Inertia::render('Admin/MarketingCampaigns/Edit', [
            'campaign' => $marketingCampaign,
            'emailTemplates' => $emailTemplates,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarketingCampaign $marketingCampaign)
    {
        // Only allow updating draft or scheduled campaigns
        if (! in_array($marketingCampaign->status, ['draft', 'scheduled', 'paused'])) {
            return back()->withErrors([
                'message' => 'Cannot update a campaign that is sending, sent, or cancelled.',
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms,push,notification',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'audience_type' => 'required|in:all_users,segment,custom',
            'audience_filters' => 'nullable|array',
            'recipient_ids' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
            'is_ab_test' => 'boolean',
            'ab_test_config' => 'nullable|array',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $marketingCampaign->name) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (MarketingCampaign::where('slug', $validated['slug'])->where('id', '!=', $marketingCampaign->id)->exists()) {
                $validated['slug'] = $originalSlug.'-'.$counter++;
            }
        }

        // Update status based on scheduled_at
        if (isset($validated['scheduled_at']) && $marketingCampaign->status === 'draft') {
            $validated['status'] = 'scheduled';
        }

        // Recalculate total recipients
        if ($validated['audience_type'] === 'all_users') {
            $validated['total_recipients'] = User::count();
        } elseif ($validated['audience_type'] === 'custom' && isset($validated['recipient_ids'])) {
            $validated['total_recipients'] = count($validated['recipient_ids']);
        }

        $marketingCampaign->update($validated);

        return redirect()->route('admin.marketing-campaigns.show', $marketingCampaign->id)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingCampaign $marketingCampaign)
    {
        // Only allow deleting draft campaigns
        if ($marketingCampaign->status !== 'draft') {
            return back()->withErrors([
                'message' => 'Only draft campaigns can be deleted. Cancel the campaign instead.',
            ]);
        }

        $marketingCampaign->delete();

        return redirect()->route('admin.marketing-campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Pause a campaign.
     */
    public function pause(MarketingCampaign $campaign)
    {
        if (! in_array($campaign->status, ['scheduled', 'sending'])) {
            return back()->withErrors([
                'message' => 'Only scheduled or sending campaigns can be paused.',
            ]);
        }

        $campaign->update(['status' => 'paused']);

        return back()->with('success', 'Campaign paused successfully.');
    }

    /**
     * Resume a paused campaign.
     */
    public function resume(MarketingCampaign $campaign)
    {
        if ($campaign->status !== 'paused') {
            return back()->withErrors([
                'message' => 'Only paused campaigns can be resumed.',
            ]);
        }

        $campaign->update(['status' => 'scheduled']);

        return back()->with('success', 'Campaign resumed successfully.');
    }

    /**
     * Cancel a campaign.
     */
    public function cancel(MarketingCampaign $campaign)
    {
        if (! in_array($campaign->status, ['scheduled', 'paused', 'sending'])) {
            return back()->withErrors([
                'message' => 'Only scheduled, paused, or sending campaigns can be cancelled.',
            ]);
        }

        $campaign->update(['status' => 'cancelled']);

        return back()->with('success', 'Campaign cancelled successfully.');
    }

    /**
     * Duplicate a campaign.
     */
    public function duplicate(MarketingCampaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name.' (Copy)';
        $newCampaign->slug = Str::slug($newCampaign->name);

        $originalSlug = $newCampaign->slug;
        $counter = 1;
        while (MarketingCampaign::where('slug', $newCampaign->slug)->exists()) {
            $newCampaign->slug = $originalSlug.'-'.$counter++;
        }

        $newCampaign->status = 'draft';
        $newCampaign->created_by = auth()->id();
        $newCampaign->scheduled_at = null;
        $newCampaign->started_at = null;
        $newCampaign->completed_at = null;
        $newCampaign->sent_count = 0;
        $newCampaign->delivered_count = 0;
        $newCampaign->opened_count = 0;
        $newCampaign->clicked_count = 0;
        $newCampaign->bounced_count = 0;
        $newCampaign->unsubscribed_count = 0;
        $newCampaign->save();

        return redirect()->route('admin.marketing-campaigns.edit', $newCampaign->id)
            ->with('success', 'Campaign duplicated successfully.');
    }
}
