@extends('layouts.app')

@section('content')

<div class="container">
    <h1>Sponsors</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('sponsors.create') }}" class="btn btn-primary mb-3">
        Add Sponsor
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Tournament</th>
                <th>Sponsorship Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @foreach($sponsors as $sponsor)

            <tr>
                <td>{{ $sponsor->name }}</td>

                <td>
                    {{ $sponsor->tournament->name ?? 'N/A' }}
                </td>

                <td>
                    {{ $sponsor->sponsorship_type }}
                </td>

                <td>
                    {{ $sponsor->amount }}
                </td>

                <td>
                    {{ $sponsor->status ? 'Active' : 'Inactive' }}
                </td>

                <td>

                    <a href="{{ route('sponsors.show',$sponsor->id) }}"
                       class="btn btn-sm btn-info">
                        View
                    </a>

                    <a href="{{ route('sponsors.edit',$sponsor->id) }}"
                       class="btn btn-sm btn-warning">
                        Edit
                    </a>


                    <form action="{{ route('sponsors.destroy',$sponsor->id) }}"
                          method="POST"
                          style="display:inline">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-danger">
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>


    {{ $sponsors->links() }}

</div>


@endsection
