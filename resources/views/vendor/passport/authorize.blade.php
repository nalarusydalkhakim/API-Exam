@extends('layouts.app')

@section('content')

<style>
    .passport-authorize .scopes {
        margin-top: 20px;
    }

    .passport-authorize .buttons {
        margin-top: 25px;
        text-align: center;
    }

    .passport-authorize .btn {
        width: 125px;
    }

    .passport-authorize .btn-approve {
        margin-right: 15px;
    }

    .passport-authorize form {
        display: inline;
    }
</style>
<div class="passport-authorize">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        Permintaan Otorisasi
                    </div>
                    <div class="card-body">
                        <!-- Introduction -->
                        <p><strong>{{ $client->name }}</strong> sedang meminta izin untuk mengakses akun Anda.</p>

                        <!-- Scope List -->
                        @if (count($scopes) > 0)
                        <div class="scopes">
                            <p><strong>Aplikasi ini akan mampu:</strong></p>

                            <ul>
                                @foreach ($scopes as $scope)
                                <li>{{ $scope->description }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="buttons">

                            <!-- Cancel Button -->
                            <form method="post" action="{{ route('passport.authorizations.deny') }}">
                                @csrf
                                @method('DELETE')

                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                                <button class="btn btn-danger">Batal</button>
                            </form>
                            <!-- Authorize Button -->
                            <form method="post" action="{{ route('passport.authorizations.approve') }}">
                                @csrf

                                <input type="hidden" name="state" value="{{ $request->state }}">
                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                                <button type="submit" class="btn btn-success btn-approve">Izinkan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection