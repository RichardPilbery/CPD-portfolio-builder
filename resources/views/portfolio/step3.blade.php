@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/user/{{ $summary->user_id }}/edit">Step 1</a> / <a href="/summary/{{ $summary->id }}/edit/1">Step 2</a> / Step 3</p>

            @if($errors->has('choices'))
                <div id="flash_component" data-type="error" data-message="You need to select at least one portfolio entry."></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-paperclip"></i> Step 3: Choose your portfolio entries</h2>

        </div>
    </header>

    <main class="flex flex-wrap -mx-2">
        <div class="w-full px-2">
            <div class="w-full mb-2">

            </div>

            @if(!$portfolios->isEmpty())
                <form method="POST" action="/portfolio/step3">
                @csrf

                <div class="block w-full sm:w-1/2 md:1/4 mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="audit">Purpose of CPD profile</label>
                    <p class="mb-4">The HCPC audit process is now online, so the process for creating a profile is different for this purpose, compared to you wanting to create a profile for a PDR or job interview, for example.</p>
                    <div class="inline-block relative">
                        <select id="audit" name="audit" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                            <option value="not">CPD Profile NOT for HCPC audit</option>
                            <option value="audit">CPD Profile FOR HCPC audit</option>
                        </select>
                    </div>
                </div>
            @endif
            @forelse ($portfolios as $p)
                @if($loop->first)
                    <p class="w-full sm:w-1/2 md:1/4 mb-4 border rounded py-2 px-3 mb-2 bg-orange-200">The HCPC state that you need to carry out at least two different types of learning activity. Note that this will not include 'other'.</p>
                    <div class="w-full sm:w-1/2 md:1/4 mb-4">
                        <p class="mb-2">Here is the number of your portfolio entries stratified by activity type:</p>
                        @foreach($new as $key => $value)
                            <p class="border rounded py-2 px-3 mb-2 bg-yellow-200">{{$key}}: <b>{{$value}}</b> {{$value > 1? 'entries' : 'entry'}}</p>
                        @endforeach
                    </div>
                    <div class="w-full sm:w-1/2 md:1/4 mb-4">
                        <p class="block {{$errors->has('choices')? 'text-red-900' : 'text-gray-700'}} text-sm font-bold mb-2">Select portfolio entries to include in your profile</p>
                        <h4>{{$act_name[$p->activity_id]}}</h4>
                    @php
                            // https://laravel.com/docs/7.x/blade#loops
                            $q = $p->activity_id;
                        @endphp
                    </div>
                @elseif($p->activity_id != $q)
                    @php
                        // Update q value
                        $q = $p->activity_id;
                    @endphp
                    <div class="w-full mt-4 mb-2">
                        <h4>{{$act_name[$p->activity_id]}}</h4>
                    </div>
                @endif
                <div class="w-full mb-2 ml-4">
                    <input type="hidden" value="{{$p->id}}" name="ids[{{$p->id}}]" id="ids[{{$p->id}}]" />
                    <input type="checkbox" {{$p->profile == 1? "checked" : ""}} name="choices[{{$p->id}}]" id="choices[{{$p->id}}]"/>
                    <label for="choices[{{$p->id}}]">{{$p->title}}</label>

                </div>
            @empty
                <div class="w-full"><p>You do not have any portfolio entries that are less than 2 years old and so cannot progress beyond this point.</p></div>
                <div class="w-0 md:w-1/3 lg:m-1/3 my-10">
                    <a href="/portfolio/create"><img src="/images/text.svg"></a>
                </div>
            @endforelse
            @if(!$portfolios->isEmpty())
                <button type="submit" class="btn w-full sm:w-1/2 md:1/4 mb-4">Submit</button>
                </form>
            @endif
        </div>
    </main>

@endsection
