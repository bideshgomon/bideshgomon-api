<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationPreference;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $notificationTypes = [
            'verification_approved' => 'Verification Approved',
            'verification_rejected' => 'Verification Rejected',
            'wallet_credited' => 'Wallet Credited',
            'withdrawal_completed' => 'Withdrawal Completed',
            'job_application' => 'Job Applications',
            'visa_application' => 'Visa Applications',
            'booking_confirmed' => 'Booking Confirmations',
            'general' => 'General Notifications',
        ];

        $preferences = [];
        foreach ($notificationTypes as $type => $label) {
            $pref = UserNotificationPreference::where('user_id', $user->id)
                ->where('notification_type', $type)
                ->first();

            $preferences[] = [
                'type' => $type,
                'label' => $label,
                'email_enabled' => $pref ? $pref->email_enabled : true,
                'in_app_enabled' => $pref ? $pref->in_app_enabled : true,
                'push_enabled' => $pref ? $pref->push_enabled : false,
            ];
        }

        return Inertia::render('User/NotificationPreferences/Index', [
            'preferences' => $preferences,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.type' => 'required|string',
            'preferences.*.email_enabled' => 'required|boolean',
            'preferences.*.in_app_enabled' => 'required|boolean',
            'preferences.*.push_enabled' => 'required|boolean',
        ]);

        $user = auth()->user();

        foreach ($validated['preferences'] as $pref) {
            UserNotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $pref['type'],
                ],
                [
                    'email_enabled' => $pref['email_enabled'],
                    'in_app_enabled' => $pref['in_app_enabled'],
                    'push_enabled' => $pref['push_enabled'],
                ]
            );
        }

        return redirect()->back()->with('success', 'Notification preferences updated successfully!');
    }
}
