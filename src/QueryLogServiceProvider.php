<?php
namespace programming_cat\QueryLog;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class QueryLogServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootConfig();
    }

    /**
     * merge config to "logging.channels"
     */
    private function bootConfig() {
        this->mergeConfigFrom(
            __DIR__.'/config/logging/channels/querylog.php',
            'logging.channels.querylog'
        );
    }

}