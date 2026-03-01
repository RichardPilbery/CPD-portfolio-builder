@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / Audit Entries</p>

            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif

        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-clipboard"></i> Audit Entries</h2>
            <div class="pt-2">
                @if($show_download)
                    <a class="btn  ml-2" href="/audit/airway">Airway Log</a>
                @endif
                <a class="btn " href="/audit/create">New Audit Entry</a>
            </div>


        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
        <div class="md:flex justify-between w-full pb-4">
            @if($show_download)
                <div id="audit_log_component" data-audittype={{$audittype}} data-userid={{$userid}} data-start={{$start}} data-end={{$end}}></div>
            @endif

        </div>
            @if(count($audits))
                <div class="justify-center w-full pb-4">
                    <div id="audit_search_component"></div>                    
                </div>
                <div class="flex flex-wrap w-full -mx-2">

                    <table class="table-auto">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Inc. Date</th>
                                <th class="px-4 py-2">Inc. No.</th>
                                <th class="px-4 py-2 ">Working Impression / AMPDS</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audits as $a)
                                @php
                                    $ampds = $a->audititems->where('name', 'AMPDS code')->pluck('name2')->toArray();
                                    $ampds2 = isset($ampds[0]) ? $ampds[0] : "";
                                @endphp
                                <tr class="even:bg-gray-200 border" >
                                    <td class="px-2 py-2"><a href="/audit/{{$a->id}}">{{\Carbon\Carbon::parse($a->incdatetime)->format('d/m/y H:i')}}</a></td>
                                    <td class="px-2 py-2"><a href="/audit/{{$a->id}}">{{ $a->incnumber }}</a></td>
                                    <td class="px-2 py-2"><a href="/audit/{{$a->id}}">{{ $a->provdiag != '' ? $a->provdiag : $ampds2 }}</a></td>
                                    <td class="px-2 py-2">
                                        <div class="block sm:flex sm:justify-evenly space-x-0 sm:space-x-2">
                                        <a href="/audit/{{$a->id}}">View</a>
                                        <a href="/audit/{{$a->id}}/edit">Edit</a>
                                        <form method="POST" action="/audit/{{$a->id}}">
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
                <div class="flex flex-wrap pt-8">
                    {{ $audits->onEachSide(3)->links('vendor.pagination.tailwind') }}
                </div>
            @else
                <h3>You have no audit entries to display</h3>
            @endif
    </main>

@endsection
