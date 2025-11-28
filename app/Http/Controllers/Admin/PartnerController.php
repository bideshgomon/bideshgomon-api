<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Partner::with('updatedBy:id,name')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $partners = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Partners/Index', [
            'partners' => $partners,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Partners/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
            'website_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('partners', 'public');
        }

        $validated['updated_by'] = auth()->id();

        Partner::create($validated);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner created successfully');
    }

    public function edit(Partner $partner)
    {
        return Inertia::render('Admin/Partners/Edit', [
            'partner' => $partner
        ]);
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
            'website_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }
            $validated['logo'] = $request->file('logo')->store('partners', 'public');
        }

        $validated['updated_by'] = auth()->id();

        $partner->update($validated);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner updated successfully');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner deleted successfully');
    }

    public function toggleActive(Partner $partner)
    {
        $partner->update([
            'is_active' => !$partner->is_active,
            'updated_by' => auth()->id()
        ]);

        return back()->with('success', 'Partner status updated');
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'partners' => 'required|array',
            'partners.*.id' => 'required|exists:partners,id',
            'partners.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['partners'] as $item) {
            Partner::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order']
            ]);
        }

        return back()->with('success', 'Order updated successfully');
    }
}
