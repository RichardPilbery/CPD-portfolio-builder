@extends('layouts.app')

@section('content')
    <header class="flex flex-wrap mb-3 py-2">
            <p class="w-full text-gray-500"><a href="/home">Home</a> / Admin</p>
            

            @if (session('success'))
                <div id="flash_component" data-type="success" data-message="{{ session('success') }}"></div>
            @endif
        <div class="flex flex-wrap w-full justify-between z-0">
            <h2 class="pt-2">User Administration</h2>
            <a class="btn text-sm ml-1" href="/user/clients" title="Clients">Clients</a>
            <a class="btn text-sm ml-1" href="/user/migrate-portfolio" title="Migrate user details"><i class="fas fa-id-card"></i> Migrate User</a>
        </div>
    </header>

    <main class="flex flex-wrap items-center mb-3 py-2">
        @if(count($users))
            <div class="justify-center w-full pb-4">
                  <div id="user_search_component"></div>
            </div>
            <div class="flex flex-wrap -mx-2">
                <table class="table-auto">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Pin</th>
                            <th class="px-4 py-2">Role</th>
                            <th class="px-4 py-2">Service</th>
                            <th class="px-4 py-2">Last login</th>
                            <th class="px-4 py-2">Portfolio</th>
                            <th class="px-4 py-2">Audit</th>
                            <th class="px-4 py-2">Actions</th>

                    </thead>
                    <tbody>
                    @foreach ($users as $u)
                        <tr class="even:bg-gray-200 border" >
                            <td class="px-2 py-2">{{ $u->name }}</td>
                            <td class="px-2 py-2">{{ $u->email }}</td>
                            <td class="px-2 py-2">{{ $u->pin }}</td>
                            <td class="px-2 py-2">{{ $roles[$u->role_id] }}</td>
                            <td class="px-2 py-2">{{ $services[$u->service_id] }}</td>
                            <td class="px-2 py-2">{{ $u->last_login }}</td>
                            <td class="px-2 py-2">{{ $portfolio_count[$u->id] }}</td>
                            <td class="px-2 py-2">{{ $audit_count[$u->id] }}</td>
                            <td class="px-2 py-2 align-top">
                                <div class="block sm:flex sm:justify-evenly md:space-x-2">
                                <a href="/user/{{$u->id}}/0/edit">Edit</a>
                                <form method="POST" action="/user/{{$u->id}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 livewire-pagination">
                {{ $users->onEachSide(3)->links() }}
            </div>
        @else
            <div class="w-full"><p>No users found.</p></div>
        @endif
    </main>

@endsection
