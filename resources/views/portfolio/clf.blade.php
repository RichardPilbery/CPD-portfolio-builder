@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a> / Clinical Leadership Framework</p>

            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-briefcase-medical"></i> Clinical Leadership Framework</h2>
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
    <div class="md:flex justify-between w-full hidden pb-4">
        <a class="btn text-sm ml-1" href="/user/clf" title="Edit your CLF competencies"><i class="fas fa-briefcase-medical"></i> Edit CLF</a>
        <a class="btn text-sm ml-1" href="/portfolio/printclf" title="Create a PDF table of your CLF competencies"><i class="fas fa-briefcase-medical"></i> Print CLF</a>
            <a class="btn text-sm" href="/portfolio/create" title="Create a new portfolio entry"><i class="fas fa-book"></i>  Add Portfolio entry</a>
        </div>
        @if(count($user_clfs))
            <div class="md:flex justify-center w-full hidden pb-4">
                <div class="w-full sm:w-full md:w-1/3 lg:w-1/2 my-2 sm:my-0">
                    <div id="portfolio_search_component"></div>
                </div>
            </div>
            <div class="w-full">
                <p>This table shows you a summary of your Clinical Leadership Competencies and their associated portfolio entries. Items in green have more than two portfolio entries associated with them.</p>
            </div>
            <div class="flex flex-wrap -mx-2">
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 align-top" scope="col">Dimension</th>
                            <th class="px-4 py-2 align-top" scope="col">Portfolio Entries</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($portfolio_clf_array as $k)
                        <tr class="border {{$k['count'] >= 2? 'bg-green-300' : 'bg-red-300'}}" >
                            <td scope="col" class="border-l-2 py-2 px-4 align-top ">{{$k['name']}}: {{$k['element']}}</td>
                            <td scope="col" class="border-l-2 py-2 px-4 align-top">
                                @if(isset($k['portfolio']) && count($k['portfolio']))
                                    @foreach($k['portfolio'] as $p)
                                        <a href="/portfolio/{{$p['id']}}">{{ \Carbon\Carbon::parse($p['actdate'])->format('d/m/Y') }}: {{ $p['title'] }}</a>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="w-full pb-20"><p>You have not yet specified which CLFs competencies relate to your job role. Why not <a href="/user/clf">do this now?</a></p></div>
        @endif
    </main>

@endsection
