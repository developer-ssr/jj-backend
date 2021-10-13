<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <!-- <x-jet-authentication-card-logo /> -->
            <!-- <img src="{{ url('storage/logo/fluent.png') }}" alt=""> -->
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Some email message') }}
        </div>

    </x-jet-authentication-card>
</x-guest-layout>