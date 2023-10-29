<?php

declare(strict_types=1);

namespace MoonShine\ChangeLog;

use Illuminate\Support\ServiceProvider;

final class ChangeLogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/changelog.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'moonshine-changelog');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'moonshine-changelog');

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/moonshine-changelog'),
        ]);
    }
}
