<?php

namespace LaraZeus\Bolt\Fields\Classes;

use Filament\Forms\Components\Textarea as TextareaAlias;
use Filament\Forms\Components\TextInput;
use LaraZeus\Bolt\Fields\FieldsContract;

class Textarea extends FieldsContract
{
    public string $renderClass = TextareaAlias::class;

    public int $sort = 8;

    public function title(): string
    {
        return __('Textarea');
    }

    public static function getOptions(): array
    {
        return [
            TextInput::make('options.rows')
                ->label(__('rows')),

            TextInput::make('options.cols')
                ->label(__('cols')),

            TextInput::make('options.minLength')
                ->label(__('min length')),

            TextInput::make('options.maxLength')
                ->label(__('max length')),

            self::htmlID(),
            self::required(),
            self::columnSpanFull(),
            self::visibility(),
        ];
    }

    // @phpstan-ignore-next-line
    public function appendFilamentComponentsOptions($component, $zeusField)
    {
        parent::appendFilamentComponentsOptions($component, $zeusField);

        if (filled($zeusField['options']['maxLength'])) {
            $component->maxLength($zeusField['options']['maxLength']);
        }
        if (filled($zeusField['options']['maxLength'])) {
            $component->maxLength($zeusField['options']['maxLength']);
        }
        if (filled($zeusField['options']['rows'])) {
            $component->rows($zeusField['options']['rows']);
        }
        if (filled($zeusField['options']['cols'])) {
            $component->cols($zeusField['options']['cols']);
        }

        return $component;
    }
}
