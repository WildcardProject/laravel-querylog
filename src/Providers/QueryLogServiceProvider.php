<?php

namespace programming_cat\QueryLog\Providers;

use Illuminate\Support\ServiceProvider;

use Log;
use Illuminate\Support\Str;
use Event;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;

class QueryLogServiceProvider extends ServiceProvider
{
    private $transactionTime;
    /**
     * Bootstrap any application services.
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
        $my_config = require(__DIR__.'/../config/logging/channels/querylog.php');
        $this->app['config']->set('logging.channels.querylog', array_merge($my_config, $app_config));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // クエリーログ
        \DB::listen(function ($query) {
            $sql = $query->sql;

            $excludes = config('logging.channels.querylog.excludes', null);
            $skip = $excludes && (
                (is_callable($excludes) && $excludes($sql)) ||
                (is_string($excludes) && preg_match('/'.preg_quote($excludes).'/ui', $sql))
            );
            if ($skip) return;

            // フォーマット
            for ($i = 0; $i < count($query->bindings); $i++) {
                $sql = preg_replace("/\?/", is_int($query->bindings[$i]) ? $query->bindings[$i] : "'".$query->bindings[$i]."'" , $sql, 1);
            }
            // 記録
            Log::channel('querylog')->debug("(".$query->time."ms)[".$query->connectionName."] => ".$sql);
        });
        Event::listen(TransactionBeginning::class, function (TransactionBeginning $event) {
            $this->transactionTime = microtime(true);
            Log::channel('querylog')->debug('START TRANSACTION');
        });
        Event::listen(TransactionCommitted::class, function (TransactionCommitted $event) {
            $etime = sprintf('%0.4f', microtime(true) - $this->transactionTime);
            Log::channel('querylog')->debug("(Transaction ellapsed ".$etime." s) => COMMIT");
        });
        Event::listen(TransactionRolledBack::class, function (TransactionRolledBack $event) {
            $etime = sprintf('%0.4f', microtime(true) - $this->transactionTime);
            Log::channel('querylog')->debug("(Transaction ellapsed ".$etime." s) => ROLLBACK");
        });

    }
}
