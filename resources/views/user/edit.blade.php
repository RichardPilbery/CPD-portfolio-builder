@extends('layouts.app')
@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">

    @if($profile == 0)
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">My Portfolio Entries</a> / Edit User details</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-id-card"></i> Edit User details</h2>
        </div>
    @else
        <p class="w-full text-gray-500"><a href="/home">Home</a> / Step 1</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-paperclip"></i> Step 1: Update your details</h2>
        </div>
    @endif
</header>
<main class="flex flex-wrap -mx-2">
{{--     <ul>
        @foreach ($errors->all() as $message)
            <li>{{$message}}</li>
        @endforeach
    </ul> --}}
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        <form method="POST" action="/user/{{$user->id}}"
            class="card"
        >
            @csrf
            @method('PATCH')

            <h1 class=" mb-10 text-center">Update your details</h1>

            <input type="hidden" id="profile" name="profile" value="{{old('profile', $profile)}}" />
            <input type="hidden" id="id" name="id" value="{{old('id', $user->id)}}" />

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="name">
                    Name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('name') ? 'is-invalid' : '' }}"
                        id="name"
                        name="name"
                        type="text"
                        placeholder="Name"
                        value="{{ old('name', $user->name) }}"

                        autofocus>
                @if($errors->has('name'))
                    <div class="error-fb">
                        <sub>Please enter your name</sub>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Email"
                        value="{{ old('email', $user->email) }}"
                        required>
                @if($errors->has('email'))
                    <div class="error-fb">
                        <sub>Please enter a valid email. Emails addresses must be unique.</sub>
                    </div>
                @else
                    <small>Please enter a unique email address</small>
                @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Password"
                        value="{{ old('password', $user->password) }}"
                        required>
                @if($errors->has('password'))
                    <div class="error-fb">
                        <sub>Please enter a valid password</sub>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="pin">
                    PIN
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('pin') ? 'is-invalid' : '' }}"
                        id="pin"
                        name="pin"
                        type="text"
                        placeholder="PIN"
                        value="{{ old('pin', $user->pin) }}">
                @if($errors->has('pin'))
                    <div class="error-fb">
                        <sub>Please enter a PIN</sub>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="role_id">
                    Job Role
                </label>
                <select class="block shadow border rounded text-lg" name="role_id" id="role_id">
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ $r->id == old('role_id', $user->role_id)? "selected":"" }}>{{ $r->title }}</option>
                @endforeach
                </select>
            </div>


            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="service_id">
                    Service
                </label>
                <select class="block shadow border rounded text-lg" name="service_id" id="service_id">
                @foreach($services as $s)
                    <option value="{{ $s->id }}" {{ $s->id == old('service_id', $user->service_id)? "selected":"" }}>{{ $s->title }}</option>
                @endforeach
                </select>
            </div>

            <button type="submit" class="btn  mr-2">Update</button>

        </form>
        @admin
            <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
                <h2>Admin functions</h2>
                <form class="pt-2" method="POST" action="/user/{{$user->id}}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="danger-btn" onclick="return confirm('Are you sure you want to delete this item?');">Delete User</button>
                </form>
            </div>
        @endadmin
    </div>
</main>
@endsection
