@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/user/0/edit">User</a> / CLF </p>

            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-key"></i> Clinical Leadership Competency Framework</h2>
        </div>
    </header>

    <main class="flex flex-wrap -mx-2">
        <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
            <p class="pb-5">Your currently selected CLFs are listed below. Feel free to amend as necessary.</p>
            <form method="POST" action="/user/clf">
            @csrf

            <input type="hidden" name="clfref" id="clfref" value="{{$clfrefer}}" />

            <div class="mb-4">
                @foreach($clfs as $k)
                    <div class="w-full mb-2 ml-4">
                        <input type="checkbox" name="clf_id[{{$k->id}}]" id="clf_id[{{$k->id}}]" {{ in_array($k->id, $user_clfs) ? "checked" : "" }}/>
                        <label for="clf_id[{{$k->id}}]">{{$k->name}}: {{$k->element}}</label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn w-full sm:w-1/2 md:1/4 mb-4">Submit</button>
            </form>
        </div>
    </main>

@endsection
