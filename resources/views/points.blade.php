@extends('layouts.app')

@section('content')

    @include('partials.sidebar');
    <main class="main">
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div>

                    <points @if(auth()->check())
                            :user="{{ auth()->user() }}"
                            @endif></points>

                </div>
            </div>
        </div>
        </div>
    </main>

@endsection
