@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a> / Summary of Practice</p>

        @if (session('success'))
            <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
        @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-suitcase"></i> Summary of Practice</h2>
                @empty($summary)
                    <a class="btn pt-2" href="/summary/create/0">Create a Summary of Practice</a>
                @else
                    <a class="btn pt-2" href="/summary/{{ $summary->id }}/edit/0">Edit Summary of Practice</a>
                @endempty
        </div>
    </header>

    <main class="flex flex-wrap -mx-2">
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
        @if(!empty($summary))
            <div class="pb-2">
                <form method="POST" action="/summary/print" class="ml-2">
                    @csrf
                    <label for="summary_type_select">       
                        Download summary
                    </label>
                    <select class="block shadow border rounded text-lg bg-blue-100" name="summary_type_select" id="summary_type_select" onchange="this.form.submit()">
                    <option value="summary" >-- Choose --</option>
                        <option value="summary" >Summary only</option>
                        <option value="2" >Summary + last 2 years CPD</option>
                        <option value="3" >Summary + last 3 years CPD</option>
                        <option value="100" >Summary + all CPD</option>
                    </select>
                </form>
            </div>
        @endif
        @empty($summary)

            <div class="w-full"><p>You have not created a summary of your practice yet. Why not <a href="/summary/create/0">create one now?</a></p></div>
            <div class="w-0 md:w-1/3 lg:m-1/3 my-10">
                <a href="/summary/create/0"><img src="/images/text.svg"></a>
            </div>

        @else

            <h3 class='mb-2 mt-1'>Work Details</h3>
            <div class="card">
                <p>{{ $summary->work_details  }}</p>
            </div>
            <p class="text-right">Word count: {{str_word_count($summary->work_details)}} words.</p>

            <h3 class='mb-2 mt-1'>Service Users</h3>
            <div class="card">
                <p>{{ $summary->service_users  }}</p>
            </div>
            <p class="text-right">Word count: {{str_word_count($summary->service_users)}} words.</p>

            <h3 class='mb-2 mt-1'>Job Description</h3>
            <div class="card">
                <p>{{ $summary->job_description }}</p>
            </div>
            <p class="text-right">Word count: {{str_word_count($summary->job_description)}} words.</p>
            <h4 class="text-right py-5">Total word count: {{str_word_count($summary->work_details." ".$summary->service_users." ".$summary->job_description)}}</h4>

            <hr class="pt-10"/>

            <p>These are optional fields explaining how you meet the HCPC's standards for CPD.</p>

            <h3 class='mb-2 mt-1'>Standard 1</h3>
            <div class="card">
                <p>{{ $summary->standard1 }}</p>
            </div>

            <h3 class='mb-2 mt-1'>Standard 2</h3>
            <div class="card">
                <p>{{ $summary->standard2 }}</p>
            </div>

        @endempty
        </div>

        </main>

@endsection
