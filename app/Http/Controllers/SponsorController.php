<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\Tournament;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::with('tournament')
            ->latest()
            ->paginate(10);

        return view('sponsors.index', compact('sponsors'));
    }


    public function create()
    {
        $tournaments = Tournament::all();

        return view('sponsors.create', compact('tournaments'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'website' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'sponsorship_type' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        Sponsor::create($validated);

        return redirect()
            ->route('sponsors.index')
            ->with('success', 'Sponsor created successfully.');
    }


    public function show(Sponsor $sponsor)
    {
        return view('sponsors.show', compact('sponsor'));
    }


    public function edit(Sponsor $sponsor)
    {
        $tournaments = Tournament::all();

        return view('sponsors.edit', compact(
            'sponsor',
            'tournaments'
        ));
    }


    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'website' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'sponsorship_type' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $sponsor->update($validated);

        return redirect()
            ->route('sponsors.index')
            ->with('success', 'Sponsor updated successfully.');
    }


    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();

        return redirect()
            ->route('sponsors.index')
            ->with('success', 'Sponsor deleted successfully.');
    }
}
