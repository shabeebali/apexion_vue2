@extends('layouts.app')

@section('content')
<<<<<<< HEAD

=======
>>>>>>> 107eb19a9e088a73f123e976eee2b4a80d5da5b6
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
<<<<<<< HEAD
                <v-btn color="secondary" label="Secondary" />
=======

>>>>>>> 107eb19a9e088a73f123e976eee2b4a80d5da5b6
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
