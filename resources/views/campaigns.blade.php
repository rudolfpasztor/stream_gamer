@extends('layouts.app')


@section('content')

    @include('partials.sidebar');
    <main class="main">
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div>
                    <h3 class="page-title">Campaigns</h3>
                        <campaigns  @if(auth()->check())
                                    :user="{{ auth()->user() }}"
                                @endif></campaigns>

                    </div>
                </div>
            </div>
        </div>
    </main>


@endsection
