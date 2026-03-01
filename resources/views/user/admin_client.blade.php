@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / <a href="/admin">Admin</a>  / Clients</p>


            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2"><i class="fas fa-binoculars"></i> OAuth Clients List</h2>
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
        @if(count($clients))
            <div class="flex flex-wrap -mx-2 pb-4">
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Client ID</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Secret</th>
                            <th class="px-4 py-2">Redirect</th>
                            <th class="px-4 py-2">Actions</th>

                    </thead>
                    <tbody>
                    @foreach ($clients as $c)
                        @if($c->revoked == 0)
                            <tr class="even:bg-gray-200 border" >
                                <td class="px-2 py-2 align-top">{{ $c->id }}</td>
                                <td class="px-2 py-2 align-top">{{ $c->name }}</td>
                                <td class="px-2 py-2 align-top">{{ $c->secret }}</td>
                                <td class="px-2 py-2 align-top">{{ $c->redirect }}</td>
                                <td class="px-2 py-2 align-top"><div class="client_delete_component" data-clientid="{{$c->id}}"></div></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="w-full pb-4"><h4>You don't have any clients yet.</h4></div>
        @endif
        <div class="w-full sm:w-3/4 md:w-3/4 lg:w-3/4 px-2">
            <h2>Add Client</h2>
            <div id="client_form_component"></div>
        </div>
    </main>

@endsection
