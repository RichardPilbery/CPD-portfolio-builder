@extends('layouts.splash')

@section('content')


<div class="bg-green-200 shadow-inner">
    <div class="container mx-auto py-10 px-10">
        <h1 class="jumbo">Personal Development Plans</h1>
    </div>
    <div>

    </div>
</div>

<div class="bg-blue-200 shadow-inner ">
    <div class="container mx-auto py-10 px-10">
        <div class="w-full md:w-2/3 lg:w-2/3 mr-10">
            <p class="text-lg pb-4">An often quoted definition is: 'a structured and supported process undertaken by an individual to reflect upon their own learning, performance and / or achievement and to plan for their personal, educational and career development.'</p>

            <p class="text-lg pb-4">If you work for an NHS Trust, you should in theory create a personal development plan as part of your personal development review. If so, then this might well be sufficient. Anecdotally, I am aware that the 'support' that is highlighted in the quote above is not always provided!</p>

            <p class="text-lg pb-4">If you are a paramedic and thus eligible for audit, the inclusion of a personal development plan can help you to clearly show how you meet standard 2 of the HCPC Standards for CPD. Just in case you cannot immediately recall what this is:<br>Demonstrate that their CPD activities are a mixture of learning activities relevant to current or future practice;</p>

            <p class="text-lg pb-4">This indicates that two items have to be complied with. The first is that you have a range of activities, which is not normally too difficult to achieve. The second is that the activities are relevant to you current or future practice. A PDP can clearly indicate what your plans are for the next 12 months and this should therefore shape your CPD activities.</p>

            <p class="text-lg pb-4">This indicates that two items have to be complied with. The first is that you have a range of activities, which is not normally too difficult to achieve. The second is that the activities are relevant to you current or future practice. A PDP can clearly indicate what your plans are for the next 12 months and this should therefore shape your CPD activities.</p>

            <p class="text-lg py-4">It also has the advantage of answering the extra questions that the HCPC might ask at audit, which include:
                <ul class="list-disc pl-4">
                    <li>How did you identify your learning needs?</li>
                    <li>How did you decide on what CPD activities to undertake?</li>
                    <li>How did you make sure your mix of CPD activities is appropriate to meet your needs?</li>
                    <li>How has the mixture of learning activities been relevant to your current or future work?</li>
                </ul>
            </p>

            <p class="text-lg py-4">However, your PDP undertaken with your employer (assuming that you have had a PDR or similar appraisal), may not quite reflect what you fancy doing in the next 12 months! This is not a problem, you can just create your own using this website. If you having trouble getting going, try completing a SWOT analysis form to identify your strengths, weakness, opportunities and threats. As well as creating another portfolio entry, it can also help spring you into some objectives to go into your PDP.</p>

            <p class="text-lg py-4">It also has the advantage of answering the extra questions that the HCPC might ask at audit, which include:
                <ul class="list-disc pl-4">
                    <li>Specific</li>
                    <li>Measurable</li>
                    <li>Achievable</li>
                    <li>Realistic</li>
                    <li>Time bound</li>
                </ul>
            </p>

            <p class="text-lg py-4">So, for example that two week, all expenses trip to the Caribbean to see how their EMS works is not likely to be SMART.</p>

            <p class="text-lg pb-4">A SMART learning objective might be to update your CPD portfolio. You might do this by reviewing your current portfolio with someone and identifying what can stay, what is out of date and needs removing and what needs to be added to bring it up to scratch. Your benchmark might be to pass an HCPC audit. This is a realistic goal and the timeframe would vary depending on how long it is until the next audit. You have, for example {{ $fullDays }} days until the next audit to sort out your portfolio!</p>

        </div>
    </div>
</div>

@endsection


