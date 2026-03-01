@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/audit">Audit Entries</a> / Airway Log</p>

        @if (session('success'))
            <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
        @endif

        <div class="flex flex-wrap justify-between w-full">
            <h2 class="pt-2"><i class="fas fa-clipboard"></i> Airway Log</h2>
            
            @if(!empty($airways))
            <div>
                <div id="audit_log_component" data-audittype={{$audittype}} data-userid={{$userid}} data-start={{$first_entry}} data-end={{$end}}></div>
            </div>
            @endif
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
            @if(count($airways))
                <div class=" justify-center w-full pb-4">
                    <div id="audit_search_component"></div>                    
                </div>
                <div class="flex flex-wrap -mx-2">
                    <table class="table-auto text-left ">
                        <thead>
                            <tr>
                                <th class="px-2 py-2">Date</th>
                                <th class="px-2 py-2 hidden md:table-cell">Inc. No.</th>
                                <th class="px-2 py-2 hidden md:table-cell">Sim</th>
                                <th class="px-2 py-2 hidden md:table-cell">Age</th>
                                <th class="px-2 py-2 hidden md:table-cell">Sex</th>
                                <th class="px-2 py-2">Airway device</th>
                                <th class="px-2 py-2">Success</th>
                                <th class="px-2 py-2 hidden md:table-cell">Grade</th>
                                <th class="px-2 py-2 hidden md:table-cell">Size</th>
                                <th class="px-2 py-2 hidden md:table-cell">Bougie</th>
                                <th class="px-2 py-2 hidden md:table-cell">CO<sub>2</sub></th>
                                <th class="px-2 py-2 hidden md:table-cell">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($airways as $a)
                                <tr class="even:bg-gray-200" >
                                    <td class="px-2 py-2 align-top"><a href="/audit/{{$a['id']}}">{{\Carbon\Carbon::parse($a['incdatetime'])->format('d/m/y H:i')}}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['incnumber'] }}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['simulation'] == 1 ? 'yes' : 'no' }}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['age'] }}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['sex'] }}</a></td>
                                    <td class="px-2 py-2 align-top"><a href="/audit/{{$a['id']}}">{{ $airway_types[$a['airwaytype_id']] }}</a></td>
                                    <td class="px-2 py-2 align-top"><a href="/audit/{{$a['id']}}">{{ $a['success'] == 1 ? 'yes' : 'no'}}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['grade'] }}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['size'] }}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['bougie'] == 1 ? 'yes' : 'no' }}</a></td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell"><a href="/audit/{{$a['id']}}">{{ $a['capnography_id'] != null ? $cap_types[$a['capnography_id']] : '' }}</td>
                                    <td class="px-2 py-2 align-top hidden md:table-cell">{{ $a['notes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-wrap pt-8">
                    {{ $audits->onEachSide(3)->links('vendor.pagination.tailwind') }}
                </div>
            @else
                <h3>You have no airway log entries to display</h3>
            @endif
    </main>

@endsection
