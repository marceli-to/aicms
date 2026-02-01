<?php

namespace MarceliTo\Aicms;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use MarceliTo\Aicms\Http\Livewire\ChatPanel;

class AicmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/aicms.php', 'aicms');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'aicms');

        Livewire::component('aicms-chat-panel', ChatPanel::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/aicms.php' => config_path('aicms.php'),
            ], 'aicms-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/aicms'),
            ], 'aicms-views');
        }
    }
}
