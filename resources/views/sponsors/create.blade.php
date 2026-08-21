@extends('layouts.app')

@section('content')

<div class="container">

    <h1>Create Sponsor</h1>

    <form action="{{ route('sponsors.store') }}" method="POST">
        @csrf

        <div>
            <label>Name</label>
            <input type="text" name="name" required>
        </div>

        <br>

        <div>
            <label>Logo</label>
            <input type="text" name="logo">
        </div>

        <br>

        <div>
            <label>Website</label>
            <input type="text" name="website">
        </div>

        <br>

        <div>
            <label>Contact Person</label>
            <input type="text" name="contact_person">
        </div>

        <br>

        <div>
            <label>Email</label>
            <input type="email" name="email">
        </div>

        <br>

        <div>
            <label>Phone</label>
            <input type="text" name="phone">
        </div>

        <br>

        <div>
            <label>Sponsorship Type</label>
            <input type="text" name="sponsorship_type">
        </div>

        <br>

        <div>
            <label>Amount</label>
            <input type="number" step="0.01" name="amount">
        </div>

        <br>

        <div>
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>

        <br>

        <button type="submit">
            Create Sponsor
        </button>

    </form>

</div>

@endsection
