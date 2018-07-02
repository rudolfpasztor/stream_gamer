@extends('layouts.app')

@section('content')

    @include('partials.sidebar');
    <main class="main">
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div>

                    <end_users @if(auth()->check())
                               :auth_user="{{ auth()->user() }}"
                            @endif></end_users>

                </div>
            </div>
        </div>
        </div>
    </main>

@endsection
