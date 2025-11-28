<?php

namespace App\Policies;

use App\Models\AgencyTeamMember;
use App\Models\User;

class AgencyTeamMemberPolicy
{
    /**
     * Determine whether the user can view the team member.
     */
    public function view(User $user, AgencyTeamMember $teamMember): bool
    {
        return $user->agency_id === $teamMember->agency_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the team member.
     */
    public function update(User $user, AgencyTeamMember $teamMember): bool
    {
        return $user->agency_id === $teamMember->agency_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the team member.
     */
    public function delete(User $user, AgencyTeamMember $teamMember): bool
    {
        return $user->agency_id === $teamMember->agency_id || $user->hasRole('admin');
    }
}
