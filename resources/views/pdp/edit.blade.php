@extends('layouts.app')

@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a> / Edit PDP objective</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-shoe-prints"></i> Edit PDP objective</h2>
        </div>
</header>

<main class="flex flex-wrap -mx-2">
{{--     <ul>
        @foreach ($errors->all() as $message)
            <li>{{$message}}</li>
        @endforeach
    </ul> --}}
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        <form method="POST" action="/pdp/{{ $pdp->id }}">

            @csrf
            @method('PATCH')

            <input type="hidden" name="completed" id="completed" value="{{$pdp->completed}}" />

            <x-text-form-component label="Objective" varname="objective" placeholder="Enter your objective" classVar="{{$pdp->objective}}" />
            <x-text-form-component label="Activity" varname="activity" placeholder="Enter the planned activity" classVar="{{$pdp->activity}}"/>
            <x-text-form-component label="Measure" varname="measure" placeholder="How are you goingn to measure success?" classVar="{{$pdp->measure}}"/>
            <x-text-form-component label="Support" varname="support" placeholder="What support do you required to achieve this objective?" classVar="{{$pdp->support}}"/>
            <x-text-form-component label="Barriers" varname="barriers" placeholder="What barriers are you going to need to overcome?" classVar="{{$pdp->barriers}}"/>


            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="finishdate">
                    Planned completion date
                </label>
            <input type="date" class="h-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('finishdate')? 'is-invalid' : '' }}" id="finishdate" name="finishdate" placeholder="Enter the planned completion date" value="{{old('finishdate', $pdp->finishdate) }}"/>
            @if($errors->has('finishdate'))
                <div class="error-fb">
                    <sub>Please enter a valid date in the form: <strong>{{date('Y-m-d')}}</strong>, or leave this field blank.</sub>
                </div>
            @endif
            </div>

            <br>

            <button type="submit" class="btn w-full">Submit</button>

        </form>
    </div>
</main>
@endsection
