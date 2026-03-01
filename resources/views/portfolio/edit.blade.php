@extends('layouts.app')

@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a> / Edit Portfolio Entry</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-book"></i> Edit Portfolio Entry</h2>
        </div>
</header>

<main class="flex flex-wrap -mx-2">
{{--     <ul>
        @foreach ($errors->all() as $message)
            <li>{{$message}}</li>
        @endforeach
    </ul> --}}
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
    <form method="POST" action="/portfolio/{{ $portfolio->id }}" enctype="multipart/form-data">

            @csrf
            @method('PATCH')

            <div class="mb-4">
                <div class="flex">
                    <div class="w-3/4">
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="actdate">
                        Activity Date
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('actdate') ? 'is-invalid' : '' }}" id="actdate"  name="actdate" type="date" value="{{ old('actdate', $portfolio->actdate->format('Y-m-d')) }}">
                        @if($errors->has('actdate'))
                            <div class="error-fb">
                                <sub>Please enter a valid date</sub>
                            </div>
                        @endif
                    </div>
                    <div class="w-1/4 pl-4">
                        <label class="block uppercase text-gray-700 text-sm font-bold mb-2 text-right" for="profile">
                                CPD profile entry
                        </label>
                        <input class="float-right" type="checkbox" name="profile" id="profile" {{ $portfolio->profile == 1 ? 'checked' : ''}}>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="title">
                    Title
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('title') ? 'is-invalid' : '' }}" id="title" name="title" type="text" placeholder="Title" value="{{ old('title', $portfolio->title) }}">
                @if($errors->has('title'))
                    <div class="error-fb">
                        <sub>Please give your document a title</sub>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description" placeholder="Provide a brief description of your portfolio entry">{{ old('description', $portfolio->description) }}
                </textarea>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="benefit">
                    Benefit
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="benefit" name="benefit" placeholder="Probably the most important box on the form. Answer two questions: How has this activity improved the work you do? How will this benefit your service users?">{{ old('benefit', $portfolio->benefit) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="activity_id">
                    Activity type <i class="fas fa-info-circle" {{ Popper::theme('light')
                        ->trigger(true, true, false)
                        ->arrow()
                        ->placement('right', 'start')
                        ->size('regular')
                        ->pop('Click on the HCPC Activities button to see typical activities and the activity type they are associated with.')}}></i>
                </label>
                <div class="flex">
                    <div class="w-1/2">
                        <select class="block shadow border rounded text-lg" name="activity_id" id="activity_id">
                        @foreach($act as $a)
                            <option value="{{ $a->id }}" {{ $a->id == old('activity_id', $portfolio->activity_id)? "selected":"" }}>{{ $a->name }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="ml-4 w-1/2">
                        <button type="button" class="btn" {{ Popper::theme('light')
                                ->trigger(false, false, true)
                                ->arrow()
                                ->placement('right', 'start')
                                ->size('regular')
                                ->pop('
                                    <ul>
                                        <li><b>Work-based learning</b></li>
                                        <li>Learning by doing</li>
                                        <li>Case studies</li>
                                        <li>Reflective practice</li>
                                        <li>Audit of service users</li>
                                        <li>Coaching from others</li>
                                        <li>Discussions with colleagues</li>
                                        <li>Peer review</li>
                                        <li>Work shadowing</li>
                                        <li>Secondments</li>
                                        <li>Job rotation</li>
                                        <li>Journal club</li>
                                        <li>In-service training</li>
                                        <li>Supervising staff or students</li>
                                        <li>Expanding your role</li>
                                        <li>Significant analysis of events</li>
                                        <li>Project work</li>
                                        <li>Filling in self-assessment questionnaires</li>
                                        <li>Gaining and learning from experience</li>
                                        <li>Involvement in the wider, profession-related work of your employer (for example, being a representative on a committee)</li>
                                    </ul>
                                    <br>
                                    <ul>
                                        <li><b>Professional Activities</b></li>
                                        <li>Lecturing or teaching</li>
                                        <li>Mentoring</li>
                                        <li>Being an examiner</li>
                                        <li>Being a tutor</li>
                                        <li>Involvement in a professional body, specialist-interest group or other groups</li>
                                        <li>Maintaining or developing specialist skills</li>
                                        <li>Giving presentations at conferences</li>
                                        <li>Organising journal clubs or other specialist groups</li>
                                        <li>Organising accredited courses</li>
                                        <li>Being an expert witness</li>
                                        <li>Supervising research or students</li>
                                        <li>Being a national assessor</li>
                                    </ul>
                                    <br>
                                    <ul>
                                        <li><b>Formal/Educational</b>
                                        <li>Courses</li>
                                        <li>Further education</li>
                                        <li>Research</li>
                                        <li>Attending conferences</li>
                                        <li>Writing articles or papers</li>
                                        <li>Going to seminars</li>
                                        <li>Distance or online learning</li>
                                        <li>Planning or running a course</li>
                                        <li>Going on courses accredited by a professional body</li>
                                    </ul>
                                    <br>
                                    <ul>
                                        <li><b>Self-directed</b></li>
                                        <li>Reading journals or articles</li>
                                        <li>Reviewing books or articles</li>
                                        <li>Keeping a file of your progress</li>
                                        <li>Updating your knowledge through the internet or TV</li>
                                    </ul>
                                    <br>
                                    <ul>
                                        <li><b>Other</b></li>
                                        <li>Relevant public service or voluntary work</li>
                                    </ul>
                                    '); 
                            }}
                            >HCPC Activities</button>
                    </div>
                </div>
            </div>
            <hr/>
            <h3 class="pt-2 pb-2">Activity Duration</h3>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="start">
                    Activity Start time/date
                </label>
            <input type="datetime-local" class="h-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('start')? 'is-invalid' : '' }}" id="start" name="start" placeholder="Enter start time/date" value="{{ old('start', Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $portfolio->start)->format('Y-m-d\TH:i')) }}"/>
            @if($errors->has('start'))
                <div class="error-fb">
                    <sub>Please enter a valid date and time in the form: <strong>{{date('Y-m-d\TH:i')}}</strong>, or leave this field blank.</sub>
                </div>
            @endif
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="end">
                    Activity End time/date
                </label>
                <input type="datetime-local" class="h-10 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('end')? 'is-invalid' : '' }}" id="end" name="end" value="{{ old('end', Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $portfolio->end)->format('Y-m-d\TH:i')) }}"/>
                @if($errors->has('end'))
                <div class="error-fb">
                    <sub>Please enter a valid date and time (after the start date/time) in the form: <strong>{{ Carbon\Carbon::now()->addMinutes(30)->format('Y-m-d\TH:i') }}</strong>, or leave this field blank.</sub>
                </div>
            @endif
            </div>

            <hr/>
            <h3 class="pt-2 pb-2">Competencies</h3>
            <p>If you wish to associate this entry with pre-existing competencies e.g. KSF, you can enter part of the name or id in the box below and choose from the drop-down list that appears. You can select multiple competencies (although only 1 at a time).</p>

            <div id="portfolio_competency_component" data-comps="{{json_encode($comps)}}" data-selcomps="{{json_encode($selected_comps)}}"></div>

            <h3 class="pt-2 pb-2">Evidence</h3>
            <p class="pb-2">You can optionally choose to upload documents to support your portfolio entry. If you don't fancy uploading stuff, simply describe what it is, or where it is below.</p>
            <p class="pb-2"> <strong>Note:</strong> You can upload images, PDFs, PowerPoint presentations, plain text files and Word documents. Note that Word and PowerPoint documents will be converted to PDF format and the original files removed.</p>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="doctitle">
                    Evidence title
                </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="doctitle" name="doctitle" type="text" placeholder="Evidence title" value="{{ old('doctitle', count($documents) > 0 ? $documents[0]->title  : '') }}">
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="docdescription">
                    Evidence description
                </label>
                <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="docdescription" name="docdescription" placeholder="Optional evidence description">{{ old('docdescription', count($documents) > 0 ? $documents[0]->description  : '') }}
                </textarea>
            </div>

            <div class="mb-4">
                <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="docformat">
                    Evidence format
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="docformat" name="docformat" type="text" placeholder="e.g 1 PDF certificate" value="{{ old('docformat', count($documents) > 0 ? $documents[0]->format  : '') }}">
            </div>

            @if(isset($documents[0]->origfilename))
                <div id="show_documents_component" data-docs="{{json_encode($documents)}}"></div>
            @endif

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

            @if(isset($portfolio->swot))
                <h3 class="pt-2 pb-2"><i class="fas fa-binoculars"></i> SWOT analysis</h3>
                <div class="mb-4">
                    <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="strength">
                        Strengths
                    </label>
                    <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('strength')? 'is-invalid' : '' }}" id="strength" name="strength" placeholder="Provide a list of strengths">{{old('strength', $portfolio->swot->strength)}}</textarea>
                    @if($errors->has('strength'))
                    <div class="error-fb">
                        <sub>Please enter a list of your strengths.</sub>
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="weakness">
                        Weaknesses
                    </label>
                    <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('weakness')? 'is-invalid' : '' }}" id="weakness" name="weakness" placeholder="List your weaknesses">{{old('weakness', $portfolio->swot->weakness)}}</textarea>
                    @if($errors->has('weakness'))
                    <div class="error-fb">
                        <sub>Please enter a list of your weaknesses.</sub>
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="opportunity">
                        Opportunities
                    </label>
                    <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('opportunity')? 'is-invalid' : '' }}" id="opportunity" name="opportunity" placeholder="List your opportunities">{{old('opportunity', $portfolio->swot->opportunity)}}</textarea>
                    @if($errors->has('opportunity'))
                    <div class="error-fb">
                        <sub>Please enter a list of your opportunities.</sub>
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block uppercase text-gray-700 text-sm font-bold mb-2" for="threat">
                        Threats
                    </label>
                    <textarea rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ $errors->has('threat')? 'is-invalid' : '' }}" id="threat" name="threat" placeholder="List your threats">{{old('threat', $portfolio->swot->threat)}}</textarea>
                    @if($errors->has('threat'))
                    <div class="error-fb">
                        <sub>Please enter a list of your threats.</sub>
                    </div>
                    @endif
                </div>

            @endif


            <hr/>
            <br>

            <button type="submit" class="btn w-full">Submit</button>

        </form>
    </div>
</main>
@endsection
