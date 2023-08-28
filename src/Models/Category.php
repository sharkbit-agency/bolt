<?php

namespace LaraZeus\Bolt\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use LaraZeus\Bolt\BoltPlugin;
use LaraZeus\Bolt\Concerns\HasUpdates;
use LaraZeus\Bolt\Database\Factories\CategoryFactory;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $updated_at
 * @property string $name
 * @property string $logo
 */
class Category extends Model
{
    use SoftDeletes;
    use HasFactory;
    use HasUpdates;
    use HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $guarded = [];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /** @return HasMany<Form> */
    public function forms(): HasMany
    {
        return $this->hasMany(BoltPlugin::getModel('Form'));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk(BoltPlugin::get()->getUploadDisk())->url($this->logo),
        );
    }
}
