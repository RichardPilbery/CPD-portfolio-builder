@extends('layouts.splash')

@section('content')


<div class="bg-green-200 shadow-inner">
    <div class="container mx-auto py-10 px-10">
        <h1 class="jumbo">Cookies</h1>
    </div>
    <div>

    </div>
</div>

<div class="bg-blue-200 shadow-inner ">
        <div class="container mx-auto py-10 px-10">
            <div class="w-full md:w-2/3 lg:w-2/3 mr-10">
                <h1 class="pb-2">What are Cookies?</h1>
                <p class="text-lg pb-4">Cookies are small text files which are stored on your computer or moblie phone by a website. Only that website can access the information on the cookie and each one is unique to your browser. This allows the website to remember things about you, such as the fact you are logged in, or the contents of your shopping basket.</p>
            </div>
        </div>
    </div>

    <div class="bg-purple-200 shadow-inner">
        <div class="container mx-auto py-10 px-10">
            <div class="w-full md:w-2/3 lg:w-2/3">
                <h1 class=" pb-2">What Cookies does the CPD Portfolio Builder website use?</h1>

                <p class="text-lg pb-4">The CPD Portfolio Builder uses two cookies to perform a range of functions. Both are essential and so cannot be disabled (otherwise the website will not work). There are no marketing or analytic cookies used on this site.</p>
                <p class="text-lg pb-4">The first is cpd_portfolio_buidler_session. This is a session cookie and is required when you log into the site in order for the website to remember who you are and display information relevant to you. Disable this and you will constantly be asked to log in again!</p>
                <p class="text-lg pb-4">The second is XSRF-TOKEN. This cookie prevents cross-site request forgery attacks (which can be achieved even though we have a secure URL...scary stuff. For more information see here: <a href="https://en.wikipedia.org/wiki/Cross-site_request_forgery">https://en.wikipedia.org/wiki/Cross-site_request_forgery</a>.</p>
            </div>
        </div>
    </div>

@endsection


