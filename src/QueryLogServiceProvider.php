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
        $app_config = config('logging.channels.querylog', []);
        $my_config = require(__DIR__.'/config/logging/channels/querylog.php');
        $this->app['config']->set('logging.channels.querylog', array_merge($my_config, $app_config));
    }

}