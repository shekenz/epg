<x-app-layout>
    <x-slot name="title">
        Media
    </x-slot>

    <div class="p-4 border-b border-gray-200">
        <a class="button-shared" href="{{ route('media.create') }}">{{ __('Upload') }}</a>
    </div>
    
    <div class="grid items-center text-gray-500
        grid-cols-2
        md:grid-cols-6
        lg:grid-cols:8
        p-2
        md:p-3
        lg:p-4
    ">
    @foreach(Auth::user()->media as $medium)
        <a class="rounded-lg hover:bg-gray-200" href="{{ route('media.display', $medium['id']) }}">
            <div class="text-center truncate p-3 md:p-3 lg:p-4">
                <img src="{{ asset('storage/uploads/'.$medium['filename']) }}">
                <span class="text-sm">{{ $medium['name'] }}</span>
            </div>
        </a>
    @endforeach
    </div>
</x-app-layout>