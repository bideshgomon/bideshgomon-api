<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use App\Services\DocumentVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminDocumentVerificationController extends Controller
{
    public function index()
    {
        $pending = UserDocument::with('user')
            ->where('status', UserDocument::STATUS_PENDING)
            ->latest()
            ->paginate(20);

        $recent = UserDocument::with(['user','reviewer'])
            ->whereIn('status', [UserDocument::STATUS_APPROVED, UserDocument::STATUS_REJECTED])
            ->latest('reviewed_at')
            ->limit(20)
            ->get();

        return Inertia::render('Admin/Documents/Verify', [
            'pending' => $pending,
            'recent' => $recent,
            'counts' => [
                'pending' => UserDocument::where('status', UserDocument::STATUS_PENDING)->count(),
                'approved' => UserDocument::where('status', UserDocument::STATUS_APPROVED)->count(),
                'rejected' => UserDocument::where('status', UserDocument::STATUS_REJECTED)->count(),
            ],
        ]);
    }

    public function approve(UserDocument $document, DocumentVerificationService $service)
    {
        $service->approve($document, Auth::id());
        return back()->with('success', 'Document approved');
    }

    public function reject(UserDocument $document, Request $request, DocumentVerificationService $service)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        $service->reject($document, Auth::id(), $request->reason);
        return back()->with('success', 'Document rejected');
    }
}
