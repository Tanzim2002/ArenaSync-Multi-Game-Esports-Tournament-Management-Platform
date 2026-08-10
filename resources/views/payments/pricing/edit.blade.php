@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="mb-4">Tournament Pricing — {{ $tournament->title }}</h3>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('tournaments.pricing.update', $tournament) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label d-block">Tournament Type</label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_paid" id="type_free"
                               value="0" {{ old('is_paid', $tournament->is_paid ? '1' : '0') == '0' ? 'checked' : '' }}
                               onchange="toggleFeeField(false)">
                        <label class="form-check-label" for="type_free">Free</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_paid" id="type_paid"
                               value="1" {{ old('is_paid', $tournament->is_paid ? '1' : '0') == '1' ? 'checked' : '' }}
                               onchange="toggleFeeField(true)">
                        <label class="form-check-label" for="type_paid">Paid</label>
                    </div>

                    @error('is_paid')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3" id="entry_fee_wrapper" style="{{ $tournament->is_paid ? '' : 'display:none;' }}">
                    <label for="entry_fee" class="form-label">Entry Fee (BDT)</label>
                    <input type="number" step="0.01" min="1" name="entry_fee" id="entry_fee"
                           class="form-control @error('entry_fee') is-invalid @enderror"
                           value="{{ old('entry_fee', $tournament->entry_fee) }}">
                    @error('entry_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">Save Pricing</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleFeeField(isPaid) {
        document.getElementById('entry_fee_wrapper').style.display = isPaid ? 'block' : 'none';
        if (!isPaid) document.getElementById('entry_fee').value = '';
    }
</script>
@endsection