<x-app-layout>
    <x-slot name="title">
        {{ ___('media') }}
    </x-slot>

    <x-slot name="controls">
       <a href="{{ route('media.create') }}" class="button big">{{ ___('upload') }}</a> 
    </x-slot>

    <div class="grid items-center text-gray-500
        grid-cols-2
        md:grid-cols-6
        lg:grid-cols:8
    ">
    @foreach($media as $medium)
        <a class="rounded-lg hover:bg-gray-200" href="{{ route('media.display', $medium->id) }}">
            <div class="text-center truncate p-3 md:p-3 lg:p-4">
                <img class="m-auto" src="{{ asset('storage/'.$medium->preset('thumb')) }}" srcset="{{ asset('storage/'.$medium->preset('thumb')) }} 1x, {{ asset('storage/'.$medium->preset('thumb2x')) }} 2x">
                <span class="text-sm">{{ $medium->name }}.{{ $medium->extension }}</span>
            </div>
        </a>
    @endforeach
    </div>
</x-app-layout>