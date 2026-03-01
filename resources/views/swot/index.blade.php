@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a>  / SWOT</p>


            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-binoculars"></i> My SWOT analyses</h2>
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
        <div class="md:flex justify-between w-full hidden pb-4">
            <a class="btn text-sm" href="/swot/create" title="Create a new SWOT analysis"><i class="fas fa-binoculars"></i>  Add SWOT</a>
        </div>
        @if(count($swots))
            <div class="flex flex-wrap -mx-2">
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Date/Activity</th>
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2 hidden md:block">Description</th>
                            <th class="px-4 py-2">Actions</th>

                    </thead>
                    <tbody>
                    @foreach ($swots as $p)
                        <tr class="even:bg-gray-200 border" >
                            <td class="px-2 py-2"><a href="/portfolio/{{$p->id}}">{{\Carbon\Carbon::parse($p->actdate)->format('d/m/Y')}}</a><abbr title="{{ $act_name[$p->activity_id] }}"><p>{{ $act_abbr[$p->activity_id]}}</p></abbr></td>
                            <td class="px-2 py-2 align-top"><a href="/portfolio/{{$p->id}}">{{ Str::words($p->title,5)}}</a></td>
                            <td class="px-2 py-2 hidden md:block align-top"><a href="/portfolio/{{$p->id}}">{{ Str::words($p->description, 10)}}</a></td>
                            <td class="px-2 py-2 align-top">
                                <div class="block sm:flex sm:justify-evenly md:space-x-2">
                                <a href="/portfolio/{{$p->id}}">View</a>
                                <a href="/portfolio/{{$p->id}}/edit">Edit</a>
                                <form method="POST" action="/portfolio/{{$p->id}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="w-full"><p>You have not undertaken a SWOT analysis yet. Why not <a href="/swot/create">create one now?</a></p></div>
            <div class="w-0 md:w-1/3 lg:m-1/3 my-10">
                <a href="/swot/create"><img src="/images/text.svg"></a>
            </div>
        @endif
    </main>

@endsection
