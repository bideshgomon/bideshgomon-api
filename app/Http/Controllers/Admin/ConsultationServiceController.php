<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
// Import our new Form Requests
use App\Http\Requests\Admin\StoreConsultationServiceRequest;
use App\Http\Requests\Admin\UpdateConsultationServiceRequest;

class ConsultationServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $services = ConsultationService::orderBy('name')->paginate(10);

        return Inertia::render('Admin/ConsultationServices/Index', [
            'services' => $services,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/ConsultationServices/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsultationServiceRequest $request): RedirectResponse
    {
        ConsultationService::create($request->validated());

        return redirect()->route('admin.consultation-services.index')
                         ->with('success', 'Consultation service created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConsultationService $consultationService): Response
    {
        return Inertia::render('Admin/ConsultationServices/Edit', [
            'service' => $consultationService,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsultationServiceRequest $request, ConsultationService $consultationService): RedirectResponse
    {
        $consultationService->update($request->validated());

        return redirect()->route('admin.consultation-services.index')
                         ->with('success', 'Consultation service updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsultationService $consultationService): RedirectResponse
    {
        $consultationService->delete();

        return redirect()->route('admin.consultation-services.index')
                         ->with('success', 'Consultation service deleted.');
    }
}