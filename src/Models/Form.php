<?php

namespace LaraZeus\Bolt\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaraZeus\Bolt\BoltPlugin;
use LaraZeus\Bolt\Concerns\HasActive;
use LaraZeus\Bolt\Concerns\HasUpdates;
use LaraZeus\Bolt\Database\Factories\FormFactory;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $updated_at
 * @property int $is_active
 * @property string $desc
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property array $options
 * @property string $extensions
 * @property string $start_date
 * @property string $end_date
 * @property bool $date_available
 * @property bool $need_login
 * @property bool $onePerUser
 */
class Form extends Model
{
    use HasActive;
    use HasFactory;
    use HasTranslations;
    use HasUpdates;
    use SoftDeletes;

    public array $translatable = ['name', 'description', 'details'];

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'options' => 'array',
        'user_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Form $form) {
            if ($form->isForceDeleting()) {
                $form->fieldsResponses()->withTrashed()->get()->each(function ($item) {
                    $item->forceDelete();
                });
                $form->responses()->withTrashed()->get()->each(function ($item) {
                    $item->forceDelete();
                });
                $form->sections()->withTrashed()->get()->each(function ($item) {
                    $item->fields()->withTrashed()->get()->each(function ($item) {
                        $item->forceDelete();
                    });
                    $item->forceDelete();
                });
            } else {
                $form->fieldsResponses->each(function ($item) {
                    $item->delete();
                });
                $form->responses->each(function ($item) {
                    $item->delete();
                });
                $form->sections->each(function ($item) {
                    $item->fields->each(function ($item) {
                        $item->delete();
                    });
                    $item->delete();
                });
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function newFactory(): FormFactory
    {
        return FormFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /** @phpstan-return BelongsTo<Form, Category> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BoltPlugin::getModel('Category'));
    }

    /** @phpstan-return hasMany<Section> */
    public function sections(): HasMany
    {
        return $this->hasMany(BoltPlugin::getModel('Section'));
    }

    /** @phpstan-return hasManyThrough<Field> */
    public function fields(): HasManyThrough
    {
        return $this->hasManyThrough(BoltPlugin::getModel('Field'), BoltPlugin::getModel('Section'));
    }

    /** @phpstan-return hasMany<Response> */
    public function responses(): hasMany
    {
        return $this->hasMany(BoltPlugin::getModel('Response'));
    }

    /** @phpstan-return hasMany<FieldResponse> */
    public function fieldsResponses(): HasMany
    {
        return $this->hasMany(BoltPlugin::getModel('FieldResponse'));
    }

    /**
     * Check if the form dates is available.
     *
     * @return Attribute<string, never>
     */
    protected function dateAvailable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_date === null ||
                (
                    $this->start_date !== null
                    && $this->end_date !== null
                    && now()->between($this->start_date, $this->end_date)
                ),
        );
    }

    /**
     * Check if the form require login.
     *
     * @return Attribute<string, never>
     */
    protected function needLogin(): Attribute
    {
        return Attribute::make(
            get: fn () => optional($this->options)['require-login'] && ! auth()->check(),
        );
    }

    public function onePerUser(): bool
    {
        return optional($this->options)['require-login']
            && optional($this->options)['one-entry-per-user']
            && $this->responses()->where('user_id', auth()->user()->id)->exists();
    }
}
