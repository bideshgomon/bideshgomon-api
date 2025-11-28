<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AgencyTeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function index()
    {
        $agency = auth()->user()->agency;

        $teamMembers = $agency->teamMembers()
            ->ordered()
            ->get();

        return Inertia::render('Agency/Team/Index', [
            'teamMembers' => $teamMembers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Agency/Team/Create');
    }

    public function store(Request $request)
    {
        $agency = auth()->user()->agency;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'qualifications' => 'nullable|array',
            'languages' => 'nullable|array',
            'years_experience' => 'nullable|integer|min:0|max:50',
            'is_visible' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $validated['agency_id'] = $agency->id;

        $teamMember = AgencyTeamMember::create($validated);

        return redirect()->route('agency.team.index')
            ->with('success', 'Team member added successfully.');
    }

    public function edit(AgencyTeamMember $teamMember)
    {
        $this->authorize('update', $teamMember);

        return Inertia::render('Agency/Team/Edit', [
            'teamMember' => $teamMember,
        ]);
    }

    public function update(Request $request, AgencyTeamMember $teamMember)
    {
        $this->authorize('update', $teamMember);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'qualifications' => 'nullable|array',
            'languages' => 'nullable|array',
            'years_experience' => 'nullable|integer|min:0|max:50',
            'is_visible' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $teamMember->update($validated);

        return back()->with('success', 'Team member updated successfully.');
    }

    public function uploadPhoto(Request $request, AgencyTeamMember $teamMember)
    {
        $this->authorize('update', $teamMember);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Delete old photo
        if ($teamMember->photo) {
            Storage::delete($teamMember->photo);
        }

        $path = $request->file('photo')->store('team-members', 'public');

        $teamMember->update(['photo' => $path]);

        return back()->with('success', 'Photo uploaded successfully.');
    }

    public function destroy(AgencyTeamMember $teamMember)
    {
        $this->authorize('delete', $teamMember);

        // Delete photo
        if ($teamMember->photo) {
            Storage::delete($teamMember->photo);
        }

        $teamMember->delete();

        return redirect()->route('agency.team.index')
            ->with('success', 'Team member deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $agency = auth()->user()->agency;

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:agency_team_members,id',
        ]);

        foreach ($request->order as $index => $memberId) {
            AgencyTeamMember::where('id', $memberId)
                ->where('agency_id', $agency->id)
                ->update(['display_order' => $index]);
        }

        return back()->with('success', 'Team members reordered successfully.');
    }
}
