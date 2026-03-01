@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a> / Key Skills Framework</p>

            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-key"></i> Key Skills Framework</h2>
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
    <div class="md:flex justify-between w-full hidden pb-4">
        <a class="btn text-sm ml-1" href="/user/ksf" title="Edit your competencies"><i class="fas fa-key"></i> Edit KSF</a>
        <a class="btn text-sm ml-1" href="/portfolio/printksf" title="Create a PDF table of your KSF competencies"><i class="fas fa-key"></i> Print KSF</a>
            <a class="btn text-sm" href="/portfolio/create" title="Create a new portfolio entry"><i class="fas fa-book"></i>  Add Portfolio entry</a>
        </div>
        @if(count($user_ksfs))
        <div class="md:flex justify-center w-full hidden pb-4">
                <div class="w-full sm:w-full md:w-1/3 lg:w-1/2 my-2 sm:my-0">
                    <div id="portfolio_search_component"></div>
                </div>
            </div>
            <div class="w-full">
                <p>This table shows you a summary of your Key Skills Framework competencies and their associated portfolio entries. Items in green have more than two portfolio entries associated with them.</p>
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
                    @foreach ($portfolio_ksf_array as $k)
                        <tr class="border {{$k['count'] >= 2? 'bg-green-300' : 'bg-red-300'}}" >
                            <td scope="col" class="border-l-2 py-2 px-4 align-top ">{{$k['name']}}: {{$k['description']}}</td>
                            <td scope="col" class="border-l-2 py-2 px-4 align-top">
                                @if(isset($k['portfolio']) && count($k['portfolio']))
                                    @foreach($k['portfolio'] as $p)
                                        @if($p['actdate'] == "00-00-00")
                                            <b>{{ $p['title'] }}</b>
                                        @else
                                            <a href="/portfolio/{{$p['id']}}">{{ \Carbon\Carbon::parse($p['actdate'])->format('d/m/Y') }}: {{ $p['title'] }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="w-full pb-20"><p>You have not yet specified which KSFs or competencies relate to your job role. Why not <a href="/user/ksf">do this now?</a></p></div>
        @endif
    </main>

@endsection
