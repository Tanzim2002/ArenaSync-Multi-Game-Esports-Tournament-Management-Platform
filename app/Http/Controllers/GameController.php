<?php

namespace App\Http\Controllers;

use App\Http\Requests\Game\StoreGameRequest;
use App\Http\Requests\Game\UpdateGameRequest;
use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Display all available games.
     */
    public function index(): View
    {
        $games = Game::query()
            ->orderBy('name')
            ->paginate(10);

        return view('games.index', compact('games'));
    }

    /**
     * Display the game creation form.
     */
    public function create(): View
    {
        return view('games.create');
    }

    /**
     * Store a newly created game.
     */
    public function store(StoreGameRequest $request): RedirectResponse
    {
        $game = Game::create($request->validated());

        return redirect()
            ->route('games.show', $game)
            ->with('success', 'Game created successfully.');
    }

    /**
     * Display a specific game.
     */
    public function show(Game $game): View
    {
        return view('games.show', compact('game'));
    }

    /**
     * Display the game editing form.
     */
    public function edit(Game $game): View
    {
        return view('games.edit', compact('game'));
    }

    /**
     * Update an existing game.
     */
    public function update(
        UpdateGameRequest $request,
        Game $game
    ): RedirectResponse {
        $game->update($request->validated());

        return redirect()
            ->route('games.show', $game)
            ->with('success', 'Game updated successfully.');
    }

    /**
     * Delete an existing game.
     */
    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return redirect()
            ->route('games.index')
            ->with('success', 'Game deleted successfully.');
    }
}