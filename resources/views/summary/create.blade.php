@extends('layouts.app')

@section('content')
<header class="flex flex-wrap items-center mb-3 py-2">
    @if($profile == 0)
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a> / Create a Summary of Practice</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-suitcase"></i> Create a Summary of Practice</h2>
        </div>
    @else
        <p class="w-full text-gray-500"><a href="/">Home</a> / <a href="/users/1/edit">Step 1</a> / Step 2</p>
        <div class="flex justify-between items-center w-full">
            <h2><i class="fas fa-paperclip"></i> Step 2: Edit/create your summary of practice</h2>
        </div>
    @endif

</header>

<main class="flex flex-wrap -mx-2">

{{--     <ul>
        @foreach ($errors->all() as $message)
            <li>{{$message}}</li>
        @endforeach
    </ul> --}}
    <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">

        <form method="POST" action="/summary">

            <p>Use the form below to edit your summary of your practice. You need to provide an overview of your current role and responsibilities. Identify any specialist areas your work in and identify the people you communicate and work with the most.</p>
            <p>For HCPC audits, there is a word limit of 500 words.</p><br/>

            @csrf

            <input type="hidden" value="{{ $profile }}" name="profile" id="profile"/>

            <div class="word_count_component"
                data-text="{{ old('work_details') }}"
                data-label="Work details"
                data-section_name="work_details"
                data-placeholder="Provide a description of your current work."
                data-popup="Start with a short paragraph about where you work, what you do and for how long you've been doing it!<br/><br/>You can mention your shift patterns, whether you work full-time / part-time and provide brief details of your work environment (e.g control, FRV, ambulance etc.).<br/><br/>Aim for 175 words."
                data-errors="{{$errors->has('work_details')}}"
                data-error_message="Please add your work details"
                data-include_wordcount="{{true}}"
                data-profile="{{true}}"
            ></div>

            <div class="word_count_component"
                data-text="{{ old('service_users') }}"
                data-label="Service Users"
                data-section_name="service_users"
                data-placeholder="Provide a description of your service users."
                data-popup="Identify your service users and other people that you come into contact with professionally. This might be patients, relatives, callers, staff, students and other health professionals. These are the groups that your portfolio entries are supposed to be benefiting.<br/><br/>Aim for 150 words."
                data-errors="{{$errors->has('service_users')}}"
                data-error_message="Please add your service users"
                data-include_wordcount="{{true}}"
                data-profile="{{true}}"
            ></div>

            <div class="word_count_component"
                data-text="{{ old('job_description') }}"
                data-label="Job Description"
                data-section_name="job_description"
                data-placeholder="Provide a job description"
                data-popup="Don't just cut and paste your job description.  Try to come up with four or five short paragraphs that sum up what you do.  Feel free to use your job desription to help you though.<br/><br/>Aim for a 175 words.  The total word count is <b>500 words</b> for the three sections."
                data-errors="{{$errors->has('job_description') }}"
                data-error_message="Please add your job description"
                data-include_wordcount="{{true}}"
                data-profile="{{true}}"
            ></div>

            <div class="mb-4">
                <p class="text-right">Total: <b><span id="word-total"></span></b> words</p>
            </div>

            <hr />

            <p class="py-10">For HCPC audits, you need to complete a short statement about how you meet the standards for CPD if required to produce a profile. Use these optional fields below to do this.</p>


            <div class="word_count_component"
                data-text="{{ old('standard1') }}"
                data-label="Standard 1"
                data-section_name="standard1"
                data-placeholder="Provide a description about how you meet Standard 1 of the HCPC standards for CPD."
                data-popup="To meet <b>Standard 1</b>, you need to: maintain a continuous, up-to-date and accurate record of their CPD activities. Briefly explain that you use an online CPD Portfolio Builder and that you have included a summary of the previous two years CPD activity on the summary sheet (which is labelled Evidence 1)."
                data-errors="{{$errors->has('standard1')}}"
                data-error_message="Please provide a description about how you meet Standard 1 of the HCPC standards for CPD."
                data-include_wordcount="{{false}}"
                data-profile="{{true}}"
                
            ></div>

            <div class="word_count_component"
                data-text="{{ old('standard2') }}"
                data-label="Standards 2, 3 and 4"
                data-section_name="standard2"
                data-placeholder="Provide a description about how you meet Standard 2 of the HCPC standards for CPD."
                data-popup="To meet <b>Standards 2, 3 and 4</b>, you need to: demonstrate that your CPD activities are a mixture of learning activities relevant to current or future practice.  Briefly explain that you have included a number of examples across a range of activities."
                data-errors="{{$errors->has('standard2')}}"
                data-error_message="Please provide a description about how you meet Standards 2, 3 and 4 of the HCPC standards for CPD."
                data-include_wordcount="{{false}}"
                data-profile="{{true}}"
            ></div>



            <button type="submit" class="btn w-full">Submit</button>

        </form>
    </div>
</main>
@endsection
