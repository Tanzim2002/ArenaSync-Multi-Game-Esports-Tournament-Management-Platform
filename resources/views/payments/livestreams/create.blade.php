@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-4">Add Livestream — {{ $tournament->title }}</h3>
            <form method="POST" action="{{ route('tournaments.livestreams.store', $tournament) }}">
                @include('payments.livestreams._form')
            </form>
        </div>
    </div>
</div>
@endsection