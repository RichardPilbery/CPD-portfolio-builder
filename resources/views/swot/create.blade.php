@extends('layouts.app')

@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a> / Create SWOT</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-binoculars"></i> Create SWOT</h2>
        </div>
</header>

<main class="flex flex-wrap -mx-2">
{{--     <ul>
        @foreach ($errors->all() as $message)
            <li>{{$message}}</li>
        @endforeach
    </ul> --}}
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        <form method="POST" action="/swot">

            @csrf

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="strength">
                    Strengths
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('strength')? 'is-invalid' : '' }}" id="strength" name="strength" placeholder="Provide a list of strengths">{{old('strength')}}</textarea>
                @if($errors->has('strength'))
                <div class="error-fb">
                    <sub>Please enter a list of your strengths.</sub>
                </div>
                @endif
            </div>



            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="weakness">
                    Weaknesses
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('weakness')? 'is-invalid' : '' }}" id="weakness" name="weakness" placeholder="List your weaknesses">{{old('weakness')}}</textarea>
                @if($errors->has('weakness'))
                <div class="error-fb">
                    <sub>Please enter a list of your weaknesses.</sub>
                </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="opportunity">
                    Opportunities
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('opportunity')? 'is-invalid' : '' }}" id="opportunity" name="opportunity" placeholder="List your opportunities">{{old('opportunity')}}</textarea>
                @if($errors->has('opportunity'))
                <div class="error-fb">
                    <sub>Please enter a list of your opportunities.</sub>
                </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="threat">
                    Threats
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('threat')? 'is-invalid' : '' }}" id="threat" name="threat" placeholder="List your threats">{{old('threat')}}</textarea>
                @if($errors->has('threat'))
                <div class="error-fb">
                    <sub>Please enter a list of your threats.</sub>
                </div>
                @endif
            </div>

            <br>

            <button type="submit" class="btn w-full">Submit</button>

        </form>
    </div>
</main>
@endsection
