@extends('layouts.app')

@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/">Home</a> / <a href="/audit">Audit Entries</a> / Create Audit Entry</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-clipboard"></i> Create Audit Entry</h2>
        </div>
</header>

<main class="flex flex-wrap -mx-2">
{{--     <ul>
        @foreach ($errors->all() as $message)
            <li>{{$message}}</li>
        @endforeach
    </ul> --}}
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        <form method="POST" action="/audit" enctype="multipart/form-data">

            @csrf

            <div class="mb-4">
                <div class="flex">
                    <div >
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="incdatetime">
                            Incident date and time
                        </label>
                        <input type="datetime-local" class="h-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('incdatetime')? 'is-invalid' : '' }}" id="incdatetime" name="incdatetime" placeholder="Enter incident date and time" value="{{old('incdatetime', Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}"/>
                        @if($errors->has('incdatetime'))
                            <div class="error-fb">
                                <sub>Please enter a valid date and time in the form: <strong>{{date('Y-m-d\TH:i')}}</strong>.</sub>
                            </div>
                        @endif
                    </div>
                    <div class="pl-2">
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="incnumber">
                            Incident number
                        </label>
                        <input type="text" class="h-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('incnumber')? 'is-invalid' : '' }}" id="incnumber" name="incnumber" placeholder="Enter incident number" value="{{old('incnumber')}}"/>
                        @if($errors->has('incnumber'))
                            <div class="error-fb">
                                <sub>Please enter a valid incident number.</sub>
                            </div>
                        @endif
                    </div>
                    <div class="pl-2">
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2 text-right" for="profile">
                                Simulation
                        </label>
                        <input class="float-right" type="checkbox" name="simulation" id="simulation">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="flex justify-start">
                    <div class="pr-2">
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="age">
                            Age
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('age') ? 'is-invalid' : '' }}" id="age" name="age" type="number" placeholder="Patient age" value="{{ old('age') }}">
                        @if($errors->has('age'))
                            <div class="error-fb">
                                <sub>Age needs to be a number</sub>
                            </div>
                        @endif
                    </div>
                    <div class="pl-1">
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="ageunit">
                    Age unit
                        </label>
                        <select class="block shadow border rounded text-lg pl-2 pr-8 py-1" name="ageunit" id="ageunit">
                            <option value="years" selected>Years</option>
                            <option value="months">Months</option>
                            <option value="days">Days</option>
                        </select>
                    </div>
                    <div class="pl-8">
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="sex">
                    Sex
                        </label>
                        <select class="block shadow border rounded text-lg pl-2 pr-8 py-1" name="sex" id="sex">
                            <option value="female" selected>Female</option>
                            <option value="male">Male</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="ampds">
                    AMPDS code
                </label>
                <select class="block shadow border rounded text-lg pl-1 pr-12 py-1" name="ampds" id="ampds">
                    <option value="" selected>-- Chooose --</option>
                @foreach($ampds as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="call_type">
                    Call type
                </label>
                <select class="block shadow border rounded text-lg pl-2 pr-12 py-1" name="call_type" id="call_type">
                @foreach($call_type as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="provdiag">
                    Working impression
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="provdiag" name="provdiag" type="text" placeholder="Working impression" value="{{ old('provdiag') }}">
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="outcome">
                    Outcome
                </label>
                <select class="block shadow border rounded text-lg pl-2 pr-8  py-1" name="outcome" id="outcome">
                    <option value="" selected>-- Chooose --</option>
                @foreach($outcome as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
                </select>
            </div>

            <hr/>
            <h3 class="pt-2 pb-2">Audit Skills</h3>
            <p>If you want to add audit skills (apart from airway management and vascular access), use this section.</p>
            <div class="mb-4 pt-3">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-4" >Skill/Procedure
                <i class="fas fa-info-circle" {{ Popper::theme('light')
                    ->trigger(false, false, true)
                    ->arrow()
                    ->placement('right', 'start')
                    ->size('regular')
                    ->pop('List of skills/procedures available:'.$list_of_skills)}}></i>
                </label>
                <div id="audit_skill_component" data-skills="{{json_encode($ai2)}}" data-selskills="{{ json_encode(old('skill', '')) }}"></div>
            </div>
            <hr/>
            <h3 class="pt-2 pb-2">Airway Management</h3>
            <div class="mb-4">
                <p>Use this section to record airway management attempts.</p>
            </div>
            <div id="airway_component" data-airway_types="{{json_encode($airway_types)}}" data-cap_types="{{json_encode($cap_types)}}" data-sel_airway="{{ json_encode(old('airway')) }}"></div>

            <h3 class="pt-2 pb-2">Vascular Access</h3>
            <div class="mb-4">
                <p>Use this section to record vascular access attempts.</p>
            </div>
            <div id="vascular_component" data-iv_types="{{json_encode($iv_types)}}" data-iv_sites="{{json_encode($iv_sites)}}" data-sel_vascular="{{ json_encode(old('vascular')) }}"></div>

            <hr/>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="note">
                    Notes
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="note" name="note" placeholder="Notes optional" value="{{ old('note') }}"></textarea>
            </div>
            <hr/>
            <h3 class="pt-2 pb-2">Evidence</h3>
            <p class="pb-2">You can optionally choose to upload documents to support your portfolio entry. If you don't fancy uploading stuff, simply describe what it is, or where it is below.</p>
            <p class="pb-2"> <strong>Note:</strong> You can upload images, PDFs, PowerPoint presentations, plain text files and Word documents. Note that Word and PowerPoint documents will be converted to PDF format and the original files removed.</p>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="doctitle">
                    Evidence title
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="doctitle" name="doctitle" type="text" placeholder="Evidence title" value="{{ old('doctitle') }}">
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="docdescription">
                    Evidence description
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="docdescription" name="docdescription" placeholder="Optional evidence description" value="{{ old('docdescription') }}"></textarea>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="docformat">
                    Evidence format
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="docformat" name=docformat type="text" placeholder="e.g 1 PDF certificate" value="{{ old('docformat') }}">
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="docupload">
                    Evidence upload
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('docupload.*') ? 'is-invalid' : '' }}" id="docupload[]" name="docupload[]" type="file" multiple>
                @if($errors->has('docupload.*'))
                    <div class="error-fb">
                        <sub>There was a problem with your chosen files. You can only upload PDFs, images, plain text files, PowerPoint files or Word documents. In addition, they must be smaller than 5Mb in size.</sub>
                    </div>
                @endif
            </div>

            <button type="submit" class="btn w-full">Submit</button>

        </form>
    </div>
</main>
@endsection
