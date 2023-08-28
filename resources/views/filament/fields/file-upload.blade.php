<div class="p-1 my-2 gap-2 flex">
    @foreach($responseValue as $file)
        <x-filament::button
            tag="a" target="_blank" size="sm" outlined
            href="{{ Storage::disk(\LaraZeus\Bolt\BoltPlugin::get()->getUploadDisk())->url($file) }}">
            {{ __('view file') .': '. $loop->iteration }}
        </x-filament::button>
    @endforeach
</div>
