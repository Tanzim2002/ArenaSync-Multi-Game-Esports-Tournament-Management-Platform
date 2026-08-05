<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function create()
{
    return view('teams.create');
}

public function show(Team $team)
{
    return view('teams.show', compact('team'));
}

public function edit(Team $team)
{
    abort_unless($team->leader_id === Auth::id(), 403);

    return view('teams.edit', compact('team'));
}
    public function store(StoreTeamRequest $request)
    {
        $data = $request->validated();

        $data['leader_id'] = Auth::id();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('team-logos', 'public');
        }

        $team = Team::create($data);

return redirect()
    ->route('teams.show', $team)
    ->with('success', 'Team created successfully.');
    }
    public function update(UpdateTeamRequest $request, Team $team)
    {
        abort_unless($team->leader_id === Auth::id(), 403);
    $data = $request->validated();

    if ($request->hasFile('logo')) {

        if ($team->logo) {
            Storage::disk('public')->delete($team->logo);
        }

        $data['logo'] = $request->file('logo')->store('team-logos', 'public');
    }

    $team->update($data);

    return redirect()->back()->with('success', 'Team updated successfully.');
    }
}
