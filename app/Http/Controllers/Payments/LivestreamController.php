<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StoreLivestreamRequest;
use App\Http\Requests\Payments\UpdateLivestreamRequest;
use App\Models\Livestream;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LivestreamController extends Controller
{

    /**
     * Display livestreams.
     */
    public function index(Tournament $tournament): View
    {
        $livestreams = $tournament
            ->livestreams()
            ->where('is_active', true)
            ->latest()
            ->get();


        return view(
            'payments.livestreams.index',
            compact(
                'tournament',
                'livestreams'
            )
        );
    }



    /**
     * Create livestream page.
     */
    public function create(Tournament $tournament): View
    {
        $this->authorizeManage($tournament);


        return view(
            'payments.livestreams.create',
            compact('tournament')
        );
    }




    /**
     * Store livestream.
     */
    public function store(
        StoreLivestreamRequest $request,
        Tournament $tournament
    ): RedirectResponse {

        $this->authorizeManage($tournament);


        $data = $request->validated();


        $tournament->livestreams()->create([

            'platform' => $data['platform'],

            'url' => $data['url'],

            'label' => $data['label'] ?? null,

            'is_active' => true,

            'added_by' => auth()->id(),

        ]);



        return redirect()
            ->route(
                'tournaments.livestreams.index',
                $tournament
            )
            ->with(
                'success',
                'Livestream link added.'
            );
    }





    /**
     * Edit livestream.
     */
    public function edit(
        Tournament $tournament,
        Livestream $livestream
    ): View {

        $this->authorizeManage($tournament);


        return view(
            'payments.livestreams.edit',
            compact(
                'tournament',
                'livestream'
            )
        );
    }





    /**
     * Update livestream.
     */
    public function update(
        UpdateLivestreamRequest $request,
        Tournament $tournament,
        Livestream $livestream
    ): RedirectResponse {


        $this->authorizeManage($tournament);


        $livestream->update(
            $request->validated()
        );


        return redirect()
            ->route(
                'tournaments.livestreams.index',
                $tournament
            )
            ->with(
                'success',
                'Livestream updated.'
            );
    }





    /**
     * Delete livestream.
     */
    public function destroy(
        Tournament $tournament,
        Livestream $livestream
    ): RedirectResponse {


        $this->authorizeManage($tournament);


        $livestream->delete();


        return redirect()
            ->route(
                'tournaments.livestreams.index',
                $tournament
            )
            ->with(
                'success',
                'Livestream removed.'
            );
    }





    /**
     * Organizer access check.
     */
    protected function authorizeManage(
        Tournament $tournament
    ): void {

        if (! auth()->check()) {
            abort(403);
        }


        if (
            (int) auth()->id()
            !==
            (int) $tournament->organizer_id
        ) {
            abort(403);
        }

    }

}