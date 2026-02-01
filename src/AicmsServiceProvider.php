<?php

namespace MarceliTo\Aicms;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use MarceliTo\Aicms\Http\Livewire\ChatPanel;
use MarceliTo\Aicms\Services\ChangeHistory;
use MarceliTo\Aicms\Services\ContentEditor;

class AicmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/aicms.php', 'aicms');

        $this->app->singleton(ChangeHistory::class);
        $this->app->singleton(ContentEditor::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'aicms');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Livewire::component('aicms-chat-panel', ChatPanel::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/aicms.php' => config_path('aicms.php'),
            ], 'aicms-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/aicms'),
            ], 'aicms-views');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'aicms-migrations');
        }
    }
}
