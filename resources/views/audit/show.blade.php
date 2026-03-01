@extends('layouts.app')

@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/audit">Audit Entries</a> / {{ $audit->incnumber }}</p>
    <div class="flex justify-between items-center w-full">
        <h2 class="pr-2"><i class="fas fa-clipboard"></i> Inc no: {{ $audit->incnumber }}</h2>
        <a class="btn" href="/audit/{{$audit->id}}/edit">Edit</a>
        <div id="audit_download_component" data-id="{{$audit->id}}" ></div>
        <form method="POST" action="/audit/{{$audit->id}}">
            @csrf
            @method('DELETE')
            <button type="submit" class="danger-btn ml-2" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
        </form>

    </div>
</header>

<main class='flex -mx-3'>
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        <h3 class='mb-2 mt-1'>Incident Date and Time</h3>
        <div class="card">
            <p>{{\Carbon\Carbon::parse($audit->incdatetime)->format('d/m/Y H:i')}}</p>
        </div>

        <div class="flex">
            <div class="w-3/4">
                <h3 class='mb-2 mt-1'>Age and sex</h3>
                <div class="card">
                    <p>{{ isset($audit->age) ? $audit->age.' '.$audit->ageunit : ''  }} {{ $audit->sex  }}</p>
                </div>
            </div>
            <div class="w-1/4 ml-4">
                <h3 class='mb-2 mt-1'>Simulation</h3>
                <div class="card">
                    <p>{{ $audit->simulation == 1? "Yes" : "No"  }}</p>
                </div>
            </div>
        </div>

        <h3 class='mb-2 mt-1'>AMPDS code</h3>
        <div class="card">
            <p>{{ $ampds }}</p>
        </div>

        <h3 class='mb-2 mt-1'>Call type</h3>
        <div class="card">
            <p>{{$call_type}}</p>
        </div>

        <h3 class='mb-2 mt-1'>Working impression</h3>
        <div class="card">
            <p>{{ !empty($audit->provdiag) ? $audit->provdiag : "Not entered"  }}</p>
        </div>

        <h3 class='mb-2 mt-1'>Outcome</h3>
        <div class="card">
            <p>{{ $outcome  }}</p>
        </div>

        @if(!$audit->airways->isEmpty())
            <hr/>
            <h2 class='mb-2 mt-1'>Airway management</h2>
            <div class="flex flex-wrap -mx-2">
                <table class="table-auto">
                    <thead>
                        <tr class="even:bg-gray-200 border">
                            <th class="px-4 py-2">Device</th>
                            <th class="px-4 py-2">Outcome</th>
                            <th class="px-4 py-2 ">Additional Information</th>
                            <th class="px-4 py-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($audit->airways as $a)
                        <tr class="even:bg-purple-200 border">
                            <td class="px-2 py-2">{{ $airway_types[$a->airwaytype_id] }}</td>
                            <td class="px-2 py-2 {{ $a->success? 'text-green-700' : 'text-red-700' }}">{{ $a->success? "Successful" : "Unsuccessful" }}</td>
                            <td class="px-2 py-2">
                                @if(in_array($a->airwaytype_id, $int_codes))
                                    <p class="">Tube size: <b>{{ $a->size }}</b></p>
                                    <p>Grade: <b>{{ $a->grade }}</b></p>
                                @else
                                    @if(in_array($a->airwaytype_id, $dev_codes))
                                        <p class="">Device size: <b>{{ !empty($a->size)? $a->size : "Not entered"}}</b></p>
                                    @endif
                                @endif
                                @if(in_array($a->airwaytype_id, $int_codes))
                                    <p>Bougie used: <b>{{ $a->bougie? "Yes" : "No" }}</b></p>
                                @endif
                                @if(in_array($a->airwaytype_id, $cap_codes))
                                    <p>Capnography: <b>{{ !empty($a->capnography_id)? $cap_types[$a->capnography_id] : "Not entered" }}</b></p>
                                @endif
                            </td>
                            <td class="px-2 py-2"><p>{{ $a->notes }}</p></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        @endif

        @if(!$ai->isEmpty())
            <hr>
            @php
                $oldheading = "";
                $newheading = "";
            @endphp
            @foreach($ai as $a)
                @php
                    if($a->name != $oldheading) {
                        // This will work except for the first loop
                        $newheading = $a->name;
                        $oldheading = $newheading;
                        if(!$loop->first) {
                            echo "</div>";
                        } else {
                            // First loop set oldheading to current value
                            $oldheading = $a->name;
                        }
                        echo "<h3 class='mb-2 mt-1'>$newheading</h3>";
                        echo "<div class='card'>";

                    }
                    echo "<p>$a->name2</p>";
                @endphp
            @endforeach
            </div>
            <hr>
        @endif

        @if(!$audit->vasculars->isEmpty())
            <hr/>
            <h2 class='mb-2 mt-1'>Vascular access</h2>
            <div class="flex flex-wrap -mx-2">
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Device</th>
                            <th class="px-4 py-2">Outcome</th>
                            <th class="px-4 py-2 ">Additional Information</th>
                            <th class="px-4 py-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($audit->vasculars as $v)
                        <tr class="even:bg-purple-200 border">
                            <td class="px-2 py-2">{{ $iv_types[$v->ivtype_id] }}</td>
                            <td class="px-2 py-2 {{ $v->success? 'text-green-700' : 'text-red-700' }}">{{ $v->success? "Successful" : "Unsuccessful" }}</td>
                            <td class="px-2 py-2">
                                <p>Size: <b>{{ !empty($v->size) ? $v->size : "Not entered" }}</b></p>
                                <p>Site: <b>{{ !empty($v->ivsite_id) ? $iv_sites[$v->ivsite_id] : "Not entered"}}</b></p>
                            </td>
                            <td class="px-2 py-2"><p>{{ $v->notes }}</p></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <hr/>
        @endif
        <h3 class='mb-2 mt-1'>Notes</h3>
        <div class="card">
            <p class="whitespace-pre-line">{{ !empty($audit->note) ? $audit->note : "No notes" }}</p>
        </div>


        <h3 class='mb-2 mt-1 pt-2'>Documents</h3>
        @if(!$documents->isEmpty())
            @foreach ($documents as $doc)
                <div class="card">
                    <div class="flex">
                        <div class='w-9/12'>
                            @if(isset($doc->origfilename))
                            <h4 class="font-light"><a href="/documents/{{$doc->id}}/download" >{{ $doc->origfilename }}</a></h4>
                            @endif
                            <p>{{ $doc->title }}</p>
                            <p>{{ $doc->description }}</p>
                            <p>{{ $doc->format }}</p>
                        </div>
                        @if(isset($doc->origfilename))
                            <div class="w-3/12">
                                <a class="float-right pr-2" href="/documents/{{$doc->id}}/download" title="Download {{$doc->title}}"><i class="fa fa-download text-3xl pt-1"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else

            <div class="card">
                <p>There are no documents or uploads associated with this entry.</p>
            </div>

        @endif

    </div>

</main>

@endsection
