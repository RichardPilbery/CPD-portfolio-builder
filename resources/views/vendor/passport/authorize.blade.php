<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <img class="h-20 w-20" src="/images/logo.svg" alt="CPD Portfolio Builder">
            </a>
        </x-slot>
        <div class="flex flex-col mt-4">
            <h4><span class="font-bold">{{ $client->name }}</span> is requesting permission to access your account.</h4>    

            <!-- Scope List -->
            @if (count($scopes) > 0)
                <div class="mt-4 w-full">
                        <h4>This application will be able to:</h4>

                        <ul class="list-disc ml-4">
                            @foreach ($scopes as $scope)
                                <li>{{ $scope->description }}</li>
                            @endforeach
                        </ul>
                </div>
            @endif
        </div>
        <div class="flex justify-full mt-8">
            <div class="mr-4">
                <form method="post" action="{{ route('passport.authorizations.approve') }}">
                    @csrf

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="btn">Authorize</button>
                </form>
            </div>
            <div>
                <form method="post" action="{{ route('passport.authorizations.deny') }}">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button class="btn danger-btn">Cancel</button>
                </form>
            </div>
        </div>
    </x-auth-card>
</x-guest-layout>

