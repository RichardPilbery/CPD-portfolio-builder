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
        <form method="POST" action="@php echo $migrate_type == 'Portfolio'? '/user/migrateP' : '/user/migrateA'; @endphp"
            class="card"
        >
            @csrf

            @if (session('error'))
                <flash-message type="error" message='{{ session('error') }}'></flash-message>
            @endif

            <h1>@php echo url('/'); @endphp</h1>

            <h1 class=" mb-10 text-center">Enter User details</h1>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="name">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="username"
                        name="username"
                        type="text"
                        placeholder="Username"
                        autofocus>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="name">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="password"
                        name="password"
                        type="text"
                        placeholder="Hashed password"
                        autofocus>
            </div>

            <button type="submit" class="btn  mr-2">Process</button>

        </form>
    </div>
</main>
@endsection
