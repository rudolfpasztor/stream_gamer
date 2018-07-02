@extends('layouts.app')

@section('content')

    @include('partials.sidebar');
    <main class="main">
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div>
                    <h3 class="page-title">Chatbot</h3>

                    <chatbot @if(auth()->check())
                             :auth_user="{{ auth()->user() }}"
                            @endif></chatbot>

                </div>
            </div>
        </div>
        </div>
    </main>
@endsection
