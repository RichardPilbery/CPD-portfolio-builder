@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/portfolio">Portfolio</a>  / PDP</p>

            @if (session('success'))
                <flash-message type="success" message='{{ session('success') }}'></flash-message>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-shoe-prints"></i> My Personal Development Plans</h2>
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
        <div class="md:flex justify-between w-full hidden pb-4">
            <a class="btn text-sm" href="/pdp/create" title="Add a PDP objective"><i class="fas fa-shoe-prints"></i>  Add PDP item</a>
            @if(count($pdps))
                <a class="btn text-sm ml-1" href="/pdp/print" title="Print your PDP"><i class="fas fa-shoe-prints"></i> Print your PDP</a>
            @endif
        </div>
        @if(count($pdps))
            <div class="flex flex-wrap -mx-2">
                <table class="table-auto">
                    <thead>
                        <tr class="text-left">
                            <th class="px-2 py-2 align-top">Objective</th>
                            <th class="px-2 py-2 hidden md:table-cell align-top">Activity</th>
                            <th class="px-2 py-2 hidden md:table-cell align-top">Measure</th>
                            <th class="px-2 py-2 hidden md:table-cell align-top">Support</th>
                            <th class="px-2 py-2 hidden md:table-cell align-top">Barriers</th>
                            <th class="px-2 py-2 align-top text-center">Planned Completion<br>Date</th>
                            <th class="px-2 py-2 align-top">Completed</th>
                            <th class="px-2 py-2 align-top">Actions</th>

                    </thead>
                    <tbody>
                    @foreach ($pdps as $p)
                        <tr class="even:bg-gray-200 border" >
                            <td class="px-2 py-2 align-top">{{ $p->objective }}</td>
                            <td class="px-2 py-2 align-top hidden md:table-cell">{{ $p->activity }}</td>
                            <td class="px-2 py-2 align-top hidden md:table-cell">{{ $p->measure }}</td>
                            <td class="px-2 py-2 align-top hidden md:table-cell">{{ $p->support }}</td>
                            <td class="px-2 py-2 align-top hidden md:table-cell">{{ $p->barriers }}</td>
                            <td class="px-2 py-2 text-center">{{\Carbon\Carbon::parse($p->finishdate)->format('d/m/Y')}}</td>
                            <td class="px-2 py-2 align-top text-center">
                                <form method="POST" action="/pdp/{{$p->id}}/toggle">
                                    @csrf
                                    @method('PATCH')
                                    <input id="completed"  name="completed" type="hidden" value="{{ $p->completed }}">
                                    <button class="aign-center" type="submit">
                                        @if($p->completed === 1)
                                            <i class="fa fa-check text-green-600 hover:text-green-800"></i>
                                        @else
                                            <i class="fa fa-times text-red-600 hover:text-red-800"></i>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="px-2 py-2 align-top">
                                <div class="block sm:flex sm:justify-evenly md:space-x-2">
                                <a href="/pdp/{{$p->id}}/edit">Edit</a>
                                <form method="POST" action="/pdp/{{$p->id}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this PDP?');">Delete</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="w-full"><p>You have not completed a personal development plan yet. Why not <a href="/pdp/create">create one now?</a></p></div>
            <div class="w-0 md:w-1/3 lg:m-1/3 my-10">
                <a href="/pdp/create"><img src="/images/text.svg"></a>
            </div>
        @endif
    </main>

@endsection
