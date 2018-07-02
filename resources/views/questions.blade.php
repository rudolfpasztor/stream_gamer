@extends('layouts.app')

@section('content')

    @include('partials.sidebar');
    <main class="main">
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div>

                <questions @if(auth()->check())
                    :auth_user="{{ auth()->user() }}"
                    @endif>
                </questions>

                </div>
            </div>
        </div>
        </div>
    </main>

@endsection