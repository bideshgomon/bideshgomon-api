<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\ConsultationService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response; // Uses the original request

class ConsultationBookingController extends Controller
{
    public function index(): Response
    {
        $services = ConsultationService::where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Public/Consultation/Index', [
            'services' => $services,
        ]);
    }

    public function showBookingForm(ConsultationService $service): Response
    {
        $consultants = User::whereHas('role', fn ($q) => $q->where('name', 'consultant'))
            ->with('consultantProfile')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Public/Consultation/Book', [
            'service' => $service,
            'consultants' => $consultants,
        ]);
    }

    // This is the original simple store method
    public function storeAppointment(StoreAppointmentRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $service = ConsultationService::findOrFail($validatedData['consultation_service_id']);

        $appointment = $request->user()->appointmentsAsClient()->create([
            'consultant_id' => $validatedData['consultant_id'],
            'consultation_service_id' => $validatedData['consultation_service_id'],
            'preferred_date' => $validatedData['preferred_date'],
            'preferred_time_slot' => $validatedData['preferred_time_slot'],
            'notes' => $validatedData['notes'] ?? null,
            'price' => $service->price,
            'status' => 'pending', // Back to 'pending'
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Your consultation request has been submitted!');
    }

    // Remove paymentSuccess and paymentCancel methods
}
