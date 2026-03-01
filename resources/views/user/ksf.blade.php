@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/user/0/edit">User</a> / KSF </p>

            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-key"></i> Key Skills Framework</h2>
        </div>
    </header>

    <main class="flex flex-wrap -mx-2">
        <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
            <p class="pb-5">Your currently selected KSF Dimensions are listed below. Feel free to amend as necessary.</p>
            <form method="POST" action="/user/ksf">
            @csrf

            <input type="hidden" name="ksfref" id="ksfref" value="{{$ksfrefer}}" />

            <div class="mb-4">
                @foreach($ksfs as $k)
                    <div class="w-full mb-2 ml-4">
                        <input type="checkbox" name="ksf_id[{{$k->id}}]" id="ksf_id[{{$k->id}}]" {{ in_array($k->id, $user_ksfs) ? "checked" : "" }}/>
                        <label for="ksf_id[{{$k->id}}]">{{$k->name}}: {{$k->description}}</label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn w-full sm:w-1/2 md:1/4 mb-4">Submit</button>
            </form>
        </div>
    </main>

@endsection
