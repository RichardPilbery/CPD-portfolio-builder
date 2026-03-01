@extends('layouts.app')

@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/">Home</a> / <a href="/portfolio">Portfolio</a> / {{ $portfolio->title }}</p>
        @if (session('success'))
            <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
        @endif
        
    <div class="flex justify-between items-center w-full">
        <h2 class="pr-2"><i class="fas fa-book"></i> {{ $portfolio->title }}</h2>
        <a class="btn" href="/portfolio/{{$portfolio->id}}/edit">Edit</a>
        <div id="portfolio_download_component" data-id="{{$portfolio->id}}" ></div>
        <form method="POST" action="/portfolio/{{$portfolio->id}}">
            @csrf
            @method('DELETE')
            <button type="submit" class="danger-btn ml-2" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
        </form>

    </div>
</header>

<main class='flex -mx-3'>
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        <h4>Activity Date: {{ $portfolio->actdate->format('d/m/Y') }}</h4>

        <h3 class='mb-2 mt-1'>Description</h3>
        <div class="card">
            <p class="whitespace-pre-line">{{ $portfolio->description  }}</p>
        </div>

        <h3 class='mb-2 mt-1'>Benefit</h3>
        <div class="card">
            <p class="whitespace-pre-line">{{ $portfolio->benefit  }}</p>
        </div>

        <h3 class='mb-2 mt-1'>Activity type</h3>
        <div class="card">
            <p>{{ $activity_name }}</p>
        </div>

        <h3 class='mb-2 mt-1'>Documents</h3>
        @forelse ($documents as $doc)
            <div class="card">
                <div class="flex">
                    <div class='w-9/12'>
                        @if(isset($doc->origfilename))
                         <h4 class="font-light"><a href="/documents/{{$doc->id}}/download" >{{ $doc->origfilename }}</a></h4>
                         @endif
                        <p>Title: {{ $doc->title }}</p>
                        <p>Description: {{ $doc->description }}</p>
                        <p>Format: {{ $doc->format }}</p>
                    </div>
                    @if(isset($doc->origfilename))
                        <div class="w-3/12">
                            <a class="float-right pr-2" href="/documents/{{$doc->id}}/download" title="Download {{$doc->title}}"><i class="fa fa-download text-3xl pt-1"></i></a>
                        </div>
                    @endif
                </div>
            </div>

        @empty

            <div class="card">
                <p>There are no documents or uploads associated with this entry.</p>
            </div>

        @endforelse

        <h3 class='mb-2 mt-1'>Competencies</h3>
            @forelse($comps as $c)

            <div class="shadow border-l-4 border-green-400 bg-green-100 px-4 py-2 mb-1">
                <p>{{ $c }}</p>
            </div>

            @empty
                <div class="card">
                    <p>There are no compentencies associated with this entry.</p>
                </div>
            @endforelse

        @if(!is_null($swot))
            <hr/>
            <h3 class='mb-2 mt-4'>SWOT analysis</h3>
            <div class="card">
                <div class="grid grid-cols-2">
                    <div class="border-r-4 border-b-4 border-blue-400 p-4">
                         <h4 class="font-light pb-2">Strengths</h4>
                        <p>{{ $swot->strength }}</p>
                    </div>
                    <div class="border-b-4 border-blue-400 p-4">
                        <h4 class="font-light pb-2">Weaknesses</h4>
                            <p>{{ $swot->weakness }}</p>
                    </div>
                    <div class="border-r-4 border-blue-400 p-4">
                        <h4 class="font-light pb-2">Opportunies</h4>
                            <p>{{ $swot->opportunity }}</p>
                    </div>
                    <div class="p-4">
                        <h4 class="font-light pb-4">Threats</h4>
                            <p>{{ $swot->threat }}</p>
                    </div>
                </div>
            </div>

        @endif

    </div>

</main>

@endsection
