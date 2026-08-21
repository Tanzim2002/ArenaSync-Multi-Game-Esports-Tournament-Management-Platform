@extends('layouts.app')

@section('content')

<div>
    <h1>Sponsor Details</h1>

    <p><b>Name:</b> {{ $sponsor->name }}</p>

    <p><b>Website:</b> {{ $sponsor->website }}</p>

    <p><b>Contact Person:</b> {{ $sponsor->contact_person }}</p>

    <p><b>Email:</b> {{ $sponsor->email }}</p>

    <p><b>Phone:</b> {{ $sponsor->phone }}</p>

    <p><b>Sponsorship Type:</b> {{ $sponsor->sponsorship_type }}</p>

    <p><b>Amount:</b> {{ $sponsor->amount }}</p>

    <p><b>Description:</b> {{ $sponsor->description }}</p>

</div>

@endsection
