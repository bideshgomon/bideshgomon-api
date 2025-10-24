<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\UpdateConsultantProfileRequest;

class ConsultantController extends Controller
{
    /**
     * Display a listing of all consultant users.
     */
    public function index(): Response
    {
        // First, find the 'consultant' role ID
        $consultantRoleId = Role::where('name', 'consultant')->value('id');

        $consultants = User::with('consultantProfile')
            ->where('role_id', $consultantRoleId)
            ->orderBy('name')
            ->paginate(10);

        return Inertia::render('Admin/Consultants/Index', [
            'consultants' => $consultants,
        ]);
    }

    /**
     * Show the form for editing the specified consultant's profile.
     * We are type-hinting the User model, but we'll call it $consultant
     */
    public function edit(User $consultant): Response
    {
        // Eager load the consultant profile
        $consultant->load('consultantProfile');

        return Inertia::render('Admin/Consultants/Edit', [
            'consultant' => $consultant,
            // Pass the profile, or a default object if it's null
            'profile' => $consultant->consultantProfile ?? (object)[
                'title' => '',
                'bio' => '',
                'specializations' => [],
            ]
        ]);
    }

    /**
     * Update the specified consultant's profile in storage.
     */
    public function update(UpdateConsultantProfileRequest $request, User $consultant): RedirectResponse
    {
        // Use updateOrCreate to either create a new profile or update an existing one
        $consultant->consultantProfile()->updateOrCreate(
            ['user_id' => $consultant->id],
            $request->validated()
        );

        return redirect()->route('admin.consultants.index')
                         ->with('success', 'Consultant profile updated.');
    }
}