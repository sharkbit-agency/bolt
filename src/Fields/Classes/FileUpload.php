<?php

namespace LaraZeus\Bolt\Fields\Classes;

use LaraZeus\Bolt\BoltPlugin;
use LaraZeus\Bolt\Facades\Bolt;
use LaraZeus\Bolt\Fields\FieldsContract;
use LaraZeus\Bolt\Models\Field;
use LaraZeus\Bolt\Models\FieldResponse;

class FileUpload extends FieldsContract
{
    public string $renderClass = \Filament\Forms\Components\FileUpload::class;

    public bool $disabled = true;

    public int $sort = 11;

    public function title(): string
    {
        return __('File Upload');
    }

    public static function getOptions(): array
    {
        return [
            \Filament\Forms\Components\Toggle::make('options.allow_multiple')->label(__('Allow Multiple')),
            self::htmlID(),
            self::required(),
            self::columnSpanFull(),
            self::visibility(),
        ];
    }

    public function getResponse(Field $field, FieldResponse $resp): string
    {
        $responseValue = (filled($resp->response) && Bolt::isJson($resp->response)) ? json_decode($resp->response) : [$resp->response];

        return view('zeus::filament.fields.file-upload')
            ->with('resp', $resp)
            ->with('responseValue', $responseValue)
            ->with('field', $field)
            ->render();
    }

    // @phpstan-ignore-next-line
    public function appendFilamentComponentsOptions($component, $zeusField)
    {
        parent::appendFilamentComponentsOptions($component, $zeusField);

        $component->disk(BoltPlugin::get()->getUploadDisk())
            ->directory(BoltPlugin::get()->getUploadDirectory());

        if (isset($zeusField->options['allow_multiple']) && $zeusField->options['allow_multiple']) {
            $component = $component->multiple();
        }

        return $component;
    }
}
