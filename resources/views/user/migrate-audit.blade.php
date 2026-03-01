@extends('layouts.app')
@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">

        <p class="w-full text-gray-500"><a href="/home">Home</a> / Migrate {{$migrate_type}}</p>
        <div class="flex justify-between items-center w-full">
            <h2>Migrate {{$migrate_type}}</h2>
        </div>

</header>
<main class="flex flex-wrap -mx-2">
{{--     <ul>
        @foreach ($errors->all() as $message)
            <li>{{$message}}</li>
        @endforeach
    </ul> --}}
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        <form method="POST" action="@php echo $migrate_type == 'P'? '/user/migrateP' : '/user/migrateA'; @endphp"
            class="card"
        >
            @csrf

            @if (session('success'))
                <flash-message type="success" message='{{ session('success') }}'></flash-message>
            @endif

            <input type="hidden" value="{{$user_id}}" name="user_id" id="user_id" />
            <input type="hidden" value="{{$olduserid}}" name="olduserid" id="olduserid" />


            <button type="submit" class="btn  mr-2">Process Audit</button>

        </form>
    </div>
</main>
@endsection
