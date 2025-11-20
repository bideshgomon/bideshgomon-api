<?php

namespace App\Http\Controllers;

use App\Models\UserPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneNumberController extends Controller
{
    /**
     * Display a listing of the user's phone numbers.
     */
    public function index()
    {
        $phoneNumbers = Auth::user()->phoneNumbers()
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($phoneNumbers);
    }

    /**
     * Store a newly created phone number.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|max:20',
            'label' => 'required|string|max:50',
            'country_code' => 'required|string|max:5',
            'is_primary' => 'boolean',
        ]);

        // Map label to phone_type (convert label to lowercase for type)
        $phoneType = strtolower($validated['label']);
        // If label is custom, default to 'mobile'
        if (!in_array($phoneType, ['mobile', 'home', 'work', 'whatsapp'])) {
            $phoneType = 'mobile';
        }

        // If setting as primary, unset other primary numbers
        if ($request->is_primary) {
            Auth::user()->phoneNumbers()->update(['is_primary' => false]);
        }

        $phoneNumber = Auth::user()->phoneNumbers()->create([
            'phone_number' => $validated['phone_number'],
            'phone_type' => $phoneType,
            'country_code' => $validated['country_code'],
            'is_primary' => $validated['is_primary'] ?? false,
        ]);

        return response()->json([
            'message' => 'Phone number added successfully',
            'phone' => $phoneNumber,
        ], 201);
    }

    /**
     * Update the specified phone number.
     */
    public function update(Request $request, UserPhoneNumber $phoneNumber)
    {
        // Check ownership
        if ($phoneNumber->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'phone_number' => 'sometimes|string|max:20',
            'label' => 'sometimes|string|max:50',
            'country_code' => 'sometimes|string|max:5',
            'is_primary' => 'boolean',
        ]);

        // Prepare update data
        $updateData = [];
        
        if (isset($validated['phone_number'])) {
            $updateData['phone_number'] = $validated['phone_number'];
        }
        
        if (isset($validated['country_code'])) {
            $updateData['country_code'] = $validated['country_code'];
        }
        
        if (isset($validated['label'])) {
            $phoneType = strtolower($validated['label']);
            if (!in_array($phoneType, ['mobile', 'home', 'work', 'whatsapp'])) {
                $phoneType = 'mobile';
            }
            $updateData['phone_type'] = $phoneType;
        }
        
        if (isset($validated['is_primary'])) {
            $updateData['is_primary'] = $validated['is_primary'];
        }

        // If setting as primary, unset other primary numbers
        if (isset($validated['is_primary']) && $validated['is_primary']) {
            Auth::user()->phoneNumbers()
                ->where('id', '!=', $phoneNumber->id)
                ->update(['is_primary' => false]);
        }

        $phoneNumber->update($updateData);

        return response()->json([
            'message' => 'Phone number updated successfully',
            'phone' => $phoneNumber->fresh(),
        ]);
    }

    /**
     * Remove the specified phone number.
     */
    public function destroy(UserPhoneNumber $phoneNumber)
    {
        // Check ownership
        if ($phoneNumber->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Don't allow deleting the only phone number
        if (Auth::user()->phoneNumbers()->count() <= 1) {
            return response()->json([
                'message' => 'You must have at least one phone number',
            ], 422);
        }

        // If deleting primary number, set another as primary
        if ($phoneNumber->is_primary) {
            $newPrimary = Auth::user()->phoneNumbers()
                ->where('id', '!=', $phoneNumber->id)
                ->first();
            
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        $phoneNumber->delete();

        return response()->json([
            'message' => 'Phone number deleted successfully',
        ]);
    }
}
