@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / Portfolio</p>

            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-book"></i> My Portfolio</h2>
            <a class="btn text-sm" href="/portfolio/create" title="Create a new portfolio entry">New portfolio entry</a>
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
        <div class="md:flex justify-between w-full hidden pb-4">
            
            <a class="btn text-sm ml-1" href="/portfolio/ksf" title="Manage your KSF competencies"><i class="fas fa-key"></i> KSF</a>
            @if($service_id != 16)
                <a class="btn text-sm ml-1" href="/portfolio/clf" title="Manage your CLF competencies"><i class="fas fa-briefcase-medical"></i> CLF</a>
            @endif
            <a class="btn text-sm ml-1" href="pdp" title="Personal development plans"><i class="fas fa-shoe-prints"></i> PDP</a>
            <a class="btn text-sm ml-1" href="swot" title="Strengths, weaknesses, opportunites and threats this way"><i class="fas fa-binoculars"></i> SWOT</a>
            <a class="btn text-sm ml-1" href="/summary" title="Edit your summary of practice"><i class="fas fa-suitcase"></i> Summary of practice</a>
            <a class="btn text-sm ml-1" href="/user/{{auth()->user()->id}}/1/edit" title="Create a CPD profile"><i class="fas fa-paperclip"></i> CPD profile</a>
        </div>

        <div class="sm:flex justify-between w-full md:hidden">
            <a class="btn text-sm ml-1" href="/portfolio/ksf" title="Manage your KSF competencies"><i class="fas fa-key"></i></a>
            @if($service_id != 16)
                <a class="btn text-sm ml-1" href="/portfolio/clf" title="Manage your CLF competencies"><i class="fas fa-briefcase-medical"></i></a>
            @endif
            <a class="btn text-sm ml-1" href="pdp" title="Personal development plans"><i class="fas fa-shoe-prints"></i></a>
            <a class="btn text-sm ml-1" href="swot" title="Strengths, weaknesses, opportunites and threats this way"><i class="fas fa-binoculars"></i></a>
            <a class="btn text-sm ml-1" href="/summary" title="Edit your summary of practice"><i class="fas fa-suitcase"></i></a>
            <a class="btn text-sm ml-1" href="/user/1/edit" title="Create a CPD profile"><i class="fas fa-paperclip"></i></a>
        </div>

        <div class="flex py-4">
            <h4>You can email the portfolio builder and automatically create an entry. Try it by sending an email to: <a class="font-bold" href="mailto:{{$user_email}}?subject=CPD%20portfolio%20entry">{{ $user_email }}</a></h4>
        </div>


        @if(count($portfolios))
            <div class="justify-center w-full pb-4">
                  <div id="portfolio_search_component"></div>
            </div>
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
                    @foreach ($portfolios as $p)
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
            <div class="mt-2 livewire-pagination">
                {{ $portfolios->onEachSide(3)->links() }}
            </div>
        @else
            <div class="w-full"><p>You do not have any portfolio entries yet. Why not <a href="/portfolios/create">create one now?</a></p></div>
            <div class="w-0 md:w-1/3 lg:m-1/3 my-10">
                <a href="/portfolios/create"><img src="/images/text.svg"></a>
            </div>
        @endif
    </main>

@endsection
