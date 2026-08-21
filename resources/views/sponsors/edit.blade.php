@extends('layouts.app')

@section('content')

<div class="container">

    <h1>Edit Sponsor</h1>

    <form action="{{ route('sponsors.update', $ponsor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>Name</label>
            <input type="text" name="name" value="{{ $sponsor->name }}" required>
        </div>

        <br>

        <div>
            <label>Logo</label>
            <input type="text" name="logo" value="{{ $sponsor->logo }}">
        </div>

        <br>

        <div>
            <label>Website</label>
            <input type="text" name="website" value="{{ $sponsor->website }}">
        </div>

        <br>

        <div>
            <label>Contact Person</label>
            <input type="text" name="contact_person" value="{{ $sponsor->contact_person }}">
        </div>

        <br>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ $sponsor->email }}">
        </div>

        <br>

        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="{{ $sponsor->phone }}">
        </div>

        <br>

        <div>
            <label>Sponsorship Type</label>
            <input type="text" name="sponsorship_type" value="{{ $sponsor->sponsorship_type }}">
        </div>

        <br>

        <div>
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ $sponsor->amount }}">
        </div>

        <br>

        <div>
            <label>Description</label>
            <textarea name="description">{{ $sponsor->description }}</textarea>
        </div>

        <br>

        <button type="submit">
            Update Sponsor
        </button>

    </form>

</div>

@endsection
