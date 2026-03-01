@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
        <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/user/{{ $summary->user_id }}/edit">Step 1</a> / <a href="/summary/{{ $summary->id }}/edit/1">Step 2</a> / <a href="/portfolio/step3" >Step 3</a> / Step 4</p>
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-paperclip"></i> Step 4: Copy/download your profile</h2>
        </div>
    </header>

    <main class="flex flex-wrap -mx-2">
        <div class="w-full px-2">
            <div class="w-full sm:w-1/2 md:1/4 mb-10">
                @if($hcpcaudit == 1)
                    <p>CPD profiles for HCPC audit must now be submitted online. No worries, we've got you covered. This page includes each section of your profile, which you can copy and paste straight into the relevant form on the HCPC audit website, and download (and then upload) your summary of practice table and evidence to support your personal statement.</p>
                @else
                    <p class="mb-4">CPD profiles for HCPC audit must now be submitted online. Since you've indicated that this profile is not for an HCPC audit, you can go ahead and just download the full profile. <b>NOTE: Generating your profile can take up to a minute to complete.</b></p>
                    <div class="portfolio_cpd_profile_download_component" data-userId="{{$user->id}}" data-url="/portfolio/profile/0/{{ $hcpcaudit }}" data-button_text="Download full CPD profile"></div>
                @endif

            </div>
        </div>

        <div class="w-full px-2">
            <div class="w-full sm:w-1/2 md:1/4 mb-4">
                <label class="block text-gray-700 text-md font-bold mb-2" for="audit_number">
                    Section 2: Summary of practice
                </label>
                <button type="button" class="copy btn w-full sm:w-3/4 mb-4" data-clipboard-target="#summary"><i class="fas fa-copy" ></i> Copy summary of practice</button>
                <div class="word_count_component"
                    data-text="    {{ $summary->work_details }}

{{ $summary->service_users }}

Outlined below are the elements of my job description which summarise my professional responsiblities:

+ {{ $summary->job_description }}"
                    data-label=""
                    data-section_name="summary"
                    data-placeholder="Summary of practice"
                    data-popup=""
                    data-errors=""
                    data-error_message=""
                    data-include_wordcount="{{true}}"
                    data-profile="{{true}}"
                ></div>

            </div>
        </div>

        <div class="w-full px-2">
            <div class="w-full sm:w-1/2 md:1/4 mb-4">
                <label class="block text-gray-700 text-md font-bold mb-2" for="audit_number">
                    Section 3: Personal statement
                </label>
                <button type="button" class="copy btn w-full sm:w-3/4 mb-4" data-clipboard-target="#statement"><i class="fas fa-copy" ></i> Copy personal statement</button>
                <div class="word_count_component"
                    data-text="3. Personal statement

{{ $summary->standard1 }}

{{ $summary->standard2 }}


"
                    data-label=""
                    data-section_name="statement"
                    data-placeholder="Statement"
                    data-popup=""
                    data-errors=""
                    data-error_message=""
                    data-include_wordcount="{{true}}"
                    data-profile="{{true}}"
                ></div>
            </div>
        </div>



        <div class="w-full px-2">
            <div class="w-full sm:w-1/2 md:1/4 mb-8">
                <label class="block text-gray-700 text-md font-bold mb-2" for="audit_number">
                    Section 4: Summary of supporting evidence
                </label>
                <p class="mb-4">Click the link below to download a summary table of your supporting evidence. You will need to upload this on the HCPC website.</p>
                <div class="portfolio_cpd_profile_download_component" data-userId="{{$user->id}}" data-url="/portfolio/profile/1/{{ $hcpcaudit }}" data-button_text="Download summary table"></div>
            </div>
        </div>

        <div class="w-full px-2">
            <div class="w-full sm:w-1/2 md:1/4 mb-4">
                <p class="mb-4">Click the link below to download the supporting evidence that are associated with the portfolio entries you included in your personal statement. You will need to upload this on the HCPC website.</p>
                <div class="portfolio_cpd_profile_download_component" data-userId="{{$user->id}}" data-url="/portfolio/profile/2/{{ $hcpcaudit }}" data-button_text="Download supporting evidence"></div>
            </div>
        </div>

    </main>
@endsection
