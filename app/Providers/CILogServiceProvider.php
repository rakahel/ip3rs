<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Components\Log\CILog;

class CILogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach(glob(app_path().'/Components/Log/*Fun.php') as $filename) {
            require_once($filename);
        }

        $this->app->bind('cilog',function() {
            return new CILog();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
