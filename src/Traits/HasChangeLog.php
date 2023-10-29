<?php

declare(strict_types=1);

namespace MoonShine\ChangeLog\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MoonShine\ChangeLog\Models\MoonshineChangeLog;
use MoonShine\MoonShineAuth;

/**
 * @mixin Model
 */
trait HasChangeLog
{
    public static function bootHasChangeLog(): void
    {
        static::saved(static function (self $row): void {
            $row->createLog();
        });
    }

    public function createLog(): void
    {
        if ($this->isDirty() && moonshineRequest()->isMoonShineRequest() && MoonShineAuth::guard()->check()) {
            $this->changeLogs()->create([
                'moonshine_user_id' => MoonShineAuth::guard()->id(),
                'states_before' => $this->getOriginal(),
                'states_after' => $this->getChanges(),
            ]);
        }
    }

    public function changeLogs(): MorphMany
    {
        return $this->morphMany(
            MoonshineChangeLog::class,
            'changelogable'
        )->orderByDesc('created_at');
    }
}
