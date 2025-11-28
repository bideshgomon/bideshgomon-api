<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['assignedTo'])
            ->where('user_id', auth()->id())
            ->latest('appointment_date');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('appointment_type', $request->type);
        }

        $appointments = $query->paginate(15);

        return Inertia::render('User/Appointments/Index', [
            'appointments' => $appointments,
            'filters' => $request->only(['status', 'type']),
        ]);
    }

    public function create()
    {
        // Get available time slots for next 30 days
        $availableSlots = $this->getAvailableSlots();

        return Inertia::render('User/Appointments/Create', [
            'availableSlots' => $availableSlots,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_type' => 'required|in:office_visit,online_meeting',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'purpose' => 'required|in:consultation,document_submission,general_inquiry,visa_interview_prep,application_review',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if slot is available
        $existingAppointment = Appointment::where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked. Please select another time.']);
        }

        // Check if appointment is within business hours (9 AM - 6 PM)
        $time = Carbon::createFromFormat('H:i', $validated['appointment_time']);
        if ($time->hour < 9 || $time->hour >= 18) {
            return back()->withErrors(['appointment_time' => 'Appointments are only available between 9:00 AM and 6:00 PM.']);
        }

        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'appointment_type' => $validated['appointment_type'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('appointments.show', $appointment->id)
            ->with('success', __('Appointment request submitted successfully. You will receive a confirmation soon.'));
    }

    public function show(Appointment $appointment)
    {
        // Ensure user can only view their own appointments
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $appointment->load(['assignedTo']);

        return Inertia::render('User/Appointments/Show', [
            'appointment' => $appointment,
        ]);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        // Ensure user can only cancel their own appointments
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$appointment->canBeCancelled()) {
            return back()->withErrors(['error' => 'This appointment cannot be cancelled.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['reason'],
        ]);

        return back()->with('success', __('Appointment cancelled successfully.'));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        // Ensure user can only reschedule their own appointments
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$appointment->canBeRescheduled()) {
            return back()->withErrors(['error' => 'This appointment cannot be rescheduled.']);
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Check if new slot is available
        $existingAppointment = Appointment::where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->where('id', '!=', $appointment->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked. Please select another time.']);
        }

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'status' => 'pending', // Reset to pending for admin to confirm
        ]);

        return back()->with('success', __('Appointment rescheduled successfully. Awaiting confirmation.'));
    }

    /**
     * Get available time slots for the next 30 days
     */
    private function getAvailableSlots(): array
    {
        $slots = [];
        $startDate = now();
        $endDate = now()->addDays(30);

        // Business hours: 9 AM to 6 PM
        $businessHours = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'
        ];

        // Get all booked appointments
        $bookedAppointments = Appointment::whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->mapWithKeys(function ($appointment) {
                return [$appointment->appointment_date->format('Y-m-d') . '_' . $appointment->appointment_time => true];
            });

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // Skip weekends
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            $dateStr = $currentDate->format('Y-m-d');
            $availableTimes = [];

            foreach ($businessHours as $time) {
                $key = $dateStr . '_' . $time;
                if (!isset($bookedAppointments[$key])) {
                    $availableTimes[] = $time;
                }
            }

            if (!empty($availableTimes)) {
                $slots[$dateStr] = $availableTimes;
            }

            $currentDate->addDay();
        }

        return $slots;
    }
}
