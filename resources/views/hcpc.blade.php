@extends('layouts.splash')

@section('content')


<div class="bg-green-200 shadow-inner">
    <div class="container mx-auto py-10 px-10">
        <h1 class="jumbo">The Health and Care Professions Council</h1>
    </div>
    <div>

    </div>
</div>

<div class="bg-blue-200 shadow-inner ">
    <div class="container mx-auto py-10 px-10">
        <div class="w-full md:w-2/3 lg:w-2/3 mr-10">
            <p class="text-lg pb-4">When you hear about the HCPC, the first thing you probably think about is <a href="https://www.hcpc-uk.org/concerns/what-we-investigate/fitness-to-practise/" title="HCPC fitness to practise (opens in new window)" target="_blank">Fitness to Practise</a>. This section is not going to address that, but instead focus on another aspect of keeping your registration - CPD audit.</p>

            <p class="text-lg pb-4">The HCPC has some good information on their requirements for CPD, so this page will not replicate them in full, but instead provide an overview of the standards for CPD and the audit process.  All of the following is based on documents that you can download from the <a href="https://www.hcpc-uk.org/cpd/" title="HCPC CPD webpage (opens in new window)" tartget="_blank">HCPC's CPD webpage</a>.</p>
        </div>
    </div>
</div>

<div class="bg-purple-200 shadow-inner">
    <div class="container mx-auto py-10 px-10">
        <div class="w-full md:w-2/3 lg:w-2/3">
            <h1 class=" pb-2">The HCPC Standards for CPD</h1>

            <p class="text-lg py-4">The HCPC has five standards for CPD, stating that registrants must:
                <ol class="list-decimal pl-4">
                    <li>Maintain a continuous, up-to-date and accurate record of their CPD activities;</li>
                    <li>Demonstrate that their CPD activities are a mixture of learning activities relevant to current or future practice;</li>
                    <li>Seek to ensure that their CPD has contributed to the quality of their practice and service delivery;</li>
                    <li>Seek to ensure that their CPD benefits the service user; and</li>
                    <li>Upon request, present a written profile (which must be their own work and supported by evidence) explaining how they have met the standards for CPD.</li>
                </ol>
            </p>

            <p class="text-lg py-4">Assuming that you are using this site to maintain your portfolio, keeping a record of your activities is easy.  In addition, there is a template built into the site, which will create a correctly formatted CPD profile for you too.  So that's 1 and 5 taken care of...all you need to do is sort 2, 3 and 4!</p>
        </div>
    </div>
</div>

<div class="bg-blue-200 shadow-inner ">
    <div class="container mx-auto py-10 px-10">
        <div class="w-full md:w-2/3 lg:w-2/3 mr-10">
            <h1 class=" pb-2">Small print</h1>

            <p class="text-lg pb-4">The following have been lifted straight from the HCPC's guide to <a href="https://www.hcpc-uk.org/globalassets/resources/guidance/continuing-professional-development-and-your-registration.pdf" title="HCPC CPD PDF document(opens in new window)" target="_blank">CPD and your registration</a>, but are important to note:</p>

            <h2>Standard 1</h2>
            <p class="text-lg py-4">You record must be continuous i.e. you need to regularly add to it.  It also needs to be up to date.  The HCPC are only interested in the last two year's worth of CPD activities.  For the next audit, that would be the period from August {{ $last_audit }} until August {{ $this_audit }}</p>

            <h2>Standard 2</h2>
            <p class="text-lg py-4">'Mixture of learning activities': The HCPC state that you need to carry out at least two different types of learning activity. The types of learning activities are:
                <ol class="list-decimal pl-4">
                    <li>Work based learning</li>
                    <li>Professional Activities</li>
                    <li>Formal / educational</li>
                    <li>Self-directed learning</li>
                    <li>Other</li>
                </ol>
            </p>

            <p class="text-lg py-4">The HCPC helpfully provides examples of each and these are provided in an 'Activity Types' box on the Add and Edit Portfolio Entry pages (you need to be logged in to see these).</p>

            <p class="text-lg py-4">'Relevant to current or future practice': Don't forget to include anything that might relate to a role you are preparing for even if it is not relevant to your current role. For example, you may have undertaken a HEMS crew course, even though you have not started on the air ambulance yet.</p>

            <h2>Standards 3 and 4</h3>
            <p class="text-lg py-4">'Seek to ensure': The HCPC recognise that despite your best efforts, the CPD you undertake may not improve the quality of your work or benefit you services users due to factors beyond your control.</p>

            <h2>Standard 5</h3>
            <p class="text-lg py-4">The HCPC have an online submission process if you are selected for audit. If you use this website for your CPD, you can create the required information automagically, enabling you to copy-and-paste certain sections and uploading a handful of files for the others.</p>

        </div>
    </div>
</div>

<div class="bg-purple-200 shadow-inner">
    <div class="container mx-auto py-10 px-10">
        <div class="w-full md:w-2/3 lg:w-2/3">
            <h1 class=" pb-2">CPD Profile</h1>

            <p class="text-lg py-4">The HCPC now have some useful and detailed guidance about how to put together and submit a CPD profile in the event that you are selected for audit. Head to the following pages: <a href="https://www.hcpc-uk.org/cpd/cpd-audits/completing-a-cpd-profile/submitting-your-profile/" title="HCPC Using CPD Online web page (opens in new window)" target="_blank">Using CPD Online</a> and <a href="https://www.hcpc-uk.org/cpd/cpd-audits/completing-a-cpd-profile/submitting-your-profile/how-to-submit-a-cpd-profile/" title="HCPC Using CPD Online web page (opens in new window)" target="_blank">How to submit a CPD profile</a>.</p>
        </div>
    </div>
</div>

@endsection


