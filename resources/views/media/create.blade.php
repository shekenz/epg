<x-app-layout>
    <x-slot name="title">
        {{ ___('upload new file') }}
    </x-slot>
    
    <x-slot name="controls">
       <a href="{{ route('media') }}" class="button-shared">{{ ___('cancel') }}</a> 
    </x-slot>

    <div class="m-4">
        @if ($errors->any())
        <div class="mb-4" :errors="$errors">
            <div class="font-medium text-red-600">
                {{ __('Whoops! Something went wrong.') }}
            </div>

            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('media.store') }}" method="post" enctype="multipart/form-data" class="flex flex-col lg:m-2">
            @csrf
            <label class="label-shared-first lg:text-lg" for="name">{{ ___('name') }} :</label>
            <input class="input-shared" id="name" name="name" type="text" value="{{ old('name') }}" >
            <label class="label-shared lg:text-lg" for="files">{{ ___('files')}} : </label>
            <input class="input-shared mt-1" id="files" name="files[]" type="file" multiple accept="image/jpeg,image/gif,image/png">
            <span class="text-gray-500 block italic">{{ ini_get('max_file_uploads') }} fichiers de {{ ini_get('upload_max_filesize') }} chacun max, au format JPG, GIF, ou PNG, pour un total maximum de {{ ini_get('post_max_size') }}</span>			
            <input class="button-shared md:px-4 md:self-end" type="submit">
        </form>
    </div>
</x-app-layout>