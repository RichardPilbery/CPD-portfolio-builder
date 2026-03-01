@extends('layouts.app')

@section('content')

<header class="flex flex-wrap items-center mb-3 py-2">
    <p class="w-full text-gray-500">Home</p>

    @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
    <h2 class="pr-2">Dashboard</h2>
</header>

<main class="flex flex-wrap mb-3 py-2">
    <div class="md:flex justify-between w-full hidden">
        <a class="btn text-sm" href="/portfolio/create" title="Create a new portfolio entry"><i class="fas fa-book"></i>  Add portfolio entry</a>
        <a class="btn text-sm ml-1" href="/audit/create" title="Create a new audit entry"><i class="fas fa-clipboard"></i> Add audit entry</a>
        <a class="btn text-sm ml-1" href="/user/{{auth()->user()->id}}/1/edit" title="Create a CPD profile"><i class="fas fa-paperclip"></i> CPD profile</a>
        <a class="btn text-sm ml-1" href="/summary" title="Edit your summary of practice"><i class="fas fa-suitcase"></i> Summary of practice</a>
        <a class="btn text-sm ml-1" href="/user/{{auth()->user()->id}}/0/edit" title="Edit your user details"><i class="fas fa-id-card"></i> Edit user</a>
    </div>

    <div class="sm:flex justify-between w-full md:hidden">
        <a class="btn" href="/portfolio/create" title="Create a new portfolio entry"><i class="fas fa-plus"></i> <i class="fas fa-book"></i></a>
        <a class="btn ml-1" href="/audit/create" title="Create a new audit entry"><i class="fas fa-plus"></i> <i class="fas fa-clipboard"></i></a>
        <a class="btn ml-1" href="/summary" title="Edit your summary of practice"><i class="fas fa-suitcase"></i></a>
        <a class="btn ml-1" href="/user/{{auth()->user()->id}}/1/edit" title="Create a CPD profile"><i class="fas fa-paperclip"></i> </a>
        <a class="btn ml-1" href="/user/{{auth()->user()->id}}/0/edit" title="Edit your user details"><i class="fas fa-id-card"></i></a>
    </div>

    <div class="flex flex-wrap pt-5 w-full">
        <div class="w-full md:w-1/2 lg:w-1/2 pr-1">
            <h2 class="px-4 pb-1">Portfolio entries</h2>
            <div class="pb-2">
                <div id="portfolio_search_component"></div>
            </div>
            <div class="mb-4 p-3 shadow-md rounded border bg-blue-100 w-full">
                @if(count($portfolios))
                    <div class="pb-4" id="portfolio_types_bar_component" data-portfolio_bar_data="{{json_encode($prep_bar)}}"></div>
                    <table class="table-auto">
                        <thead>
                            <tr>
                                <th class="px-2 py-2 text-left">Activity Date</th>
                                <th class="px-2 py-2 text-left">Title</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($portfolios as $p)
                                <tr class="even:bg-gray-200">
                                    <td class="border px-2 py-2 text-sm"><a href="/portfolio/{{$p->id}}">{{$p->actdate->format('d/m/y')}}</a></td>
                                    <td class="border px-2 py-2 text-sm"><a href="/portfolio/{{$p->id}}">{{ $p->title }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                @else
                    <h3>You have no portfolio entries to display</h3>
                @endif
            </div>
        </div>
        <div class="w-full  md:w-1/2 lg:w-1/2 pl-1 ">
            <h2 class="px-4 pb-1">Audit entries</h2>
            <div class="pb-2">
            <div id="audit_search_component"></div>
            </div>
            <div class="mb-4 p-3 shadow-md rounded border bg-purple-100 w-full">
                @if(count($audits))
                    <div class="pb-4" id="audit_line_component" data-audit_line_data="{{json_encode($audit_line_data)}}"></div>
                    <table class="table-auto">
                        <thead>
                            <tr>
                                <th class="px-2 py-2 text-left">Incident Datetime</th>
                                <th class="px-2 py-2 text-left">Incident Number</th>
                                <th class="px-2 py-2 text-left">Working Impression</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audits as $a)
                            @php
                                $ampds = $a->audititems->where('name', 'AMPDS code')->pluck('name2')->toArray();
                                $ampds2 = isset($ampds[0]) ? $ampds[0] : "";
                            @endphp
                                <tr class="even:bg-gray-200">
                                    <td class="border px-2 py-2 text-sm"><a href="/audit/{{$a->id}}">{{\Carbon\Carbon::parse($a->incdatetime)->format('d/m/y h:m')}}</a></td>
                                    <td class="border px-2 py-2 text-sm"><a href="/audit/{{$a->id}}">{{$a->incnumber}}</a></td>
                                    <td class="border px-2 py-2 text-sm"><a href="/audit/{{$a->id}}">{{ $a->provdiag != '' ? $a->provdiag : $ampds2 }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <h3>You have no audit entries to display</h3>
                @endif
            </div>
        </div>
    </div>

</main>

@endsection
