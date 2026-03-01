@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/audit">Audit Entries</a> / Airway Summary</p>

        @if (session('success'))
            <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
        @endif

        <div class="flex flex-wrap justify-between w-full">
            <h2 class="pt-2"><i class="fas fa-clipboard"></i> Audit Summary</h2>
            
            @if(!empty($audits))

            @endif
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
            <h3>Stuff here</h3>
    </main>

@endsection
