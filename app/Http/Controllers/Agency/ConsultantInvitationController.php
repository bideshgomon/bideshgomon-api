<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AgencyTeamMember;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ConsultantInvitationController extends Controller
{
    /**
     * Show invitation form
     */
    public function create()
    {
        return Inertia::render('Agency/Team/InviteConsultant');
    }

    /**
     * Send invitation to consultant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'role' => 'required|in:manager,consultant,support',
            'permissions' => 'nullable|array',
        ]);

        $agency = auth()->user()->agency;

        // Check if user already exists
        $existingUser = User::where('email', $validated['email'])->first();
        
        if ($existingUser) {
            // Check if already a team member
            if ($agency->teamMembers()->where('user_id', $existingUser->id)->exists()) {
                return back()->withErrors(['email' => 'This user is already a team member.']);
            }

            // Assign existing user to agency
            $teamMember = AgencyTeamMember::create([
                'agency_id' => $agency->id,
                'user_id' => $existingUser->id,
                'name' => $existingUser->name,
                'email' => $existingUser->email,
                'position' => $validated['position'],
                'role' => $validated['role'],
                'permissions' => $validated['permissions'] ?? $this->getDefaultPermissions($validated['role']),
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // Update user's agency relationship
            $existingUser->update(['agency_id' => $agency->id]);

            return redirect()->route('agency.team.index')
                ->with('success', 'Existing user assigned as ' . $validated['role'] . ' successfully.');
        }

        // Create invitation token
        $invitationToken = Str::random(64);

        // Create team member with invitation
        $teamMember = AgencyTeamMember::create([
            'agency_id' => $agency->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'],
            'role' => $validated['role'],
            'permissions' => $validated['permissions'] ?? $this->getDefaultPermissions($validated['role']),
            'status' => 'inactive',
            'invitation_token' => $invitationToken,
            'invited_at' => now(),
        ]);

        // Send invitation email
        $invitationUrl = route('consultant.accept-invitation', $invitationToken);
        
        // TODO: Send actual email
        // Mail::to($validated['email'])->send(new ConsultantInvitation($teamMember, $invitationUrl));

        return redirect()->route('agency.team.index')
            ->with('success', 'Invitation sent successfully to ' . $validated['email']);
    }

    /**
     * Accept invitation and register
     */
    public function acceptInvitation(Request $request, $token)
    {
        $teamMember = AgencyTeamMember::where('invitation_token', $token)
            ->whereNull('user_id')
            ->whereNotNull('invited_at')
            ->firstOrFail();

        return Inertia::render('Auth/AcceptInvitation', [
            'teamMember' => $teamMember,
            'agency' => $teamMember->agency()->select('id', 'name', 'company_name', 'logo_path')->first(),
            'token' => $token,
        ]);
    }

    /**
     * Complete invitation by creating user account
     */
    public function completeInvitation(Request $request, $token)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $teamMember = AgencyTeamMember::where('invitation_token', $token)
            ->whereNull('user_id')
            ->whereNotNull('invited_at')
            ->firstOrFail();

        DB::transaction(function () use ($teamMember, $validated) {
            // Get consultant role
            $consultantRole = Role::where('slug', 'consultant')->firstOrFail();

            // Create user account
            $user = User::create([
                'name' => $teamMember->name,
                'email' => $teamMember->email,
                'password' => bcrypt($validated['password']),
                'role_id' => $consultantRole->id,
                'agency_id' => $teamMember->agency_id,
            ]);

            // Link team member to user
            $teamMember->update([
                'user_id' => $user->id,
                'status' => 'active',
                'joined_at' => now(),
                'invitation_token' => null, // Clear token after use
            ]);
        });

        return redirect()->route('login')
            ->with('success', 'Account created successfully. You can now log in.');
    }

    /**
     * Get default permissions based on role
     */
    private function getDefaultPermissions(string $role): array
    {
        return match($role) {
            'manager' => [
                'can_view_applications' => true,
                'can_submit_quotes' => true,
                'can_view_financials' => true,
                'can_manage_team' => true,
                'can_edit_profile' => true,
            ],
            'consultant' => [
                'can_view_applications' => true,
                'can_submit_quotes' => true,
                'can_view_financials' => false,
                'can_manage_team' => false,
                'can_edit_profile' => false,
            ],
            'support' => [
                'can_view_applications' => true,
                'can_submit_quotes' => false,
                'can_view_financials' => false,
                'can_manage_team' => false,
                'can_edit_profile' => false,
            ],
            default => [],
        };
    }
}
