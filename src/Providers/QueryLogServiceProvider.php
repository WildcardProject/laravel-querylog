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
                (is_string($excludes) && preg_match('/'.preg_quote($excludes).'/', $sql))
            );
            if ($skip) return;

            // アクティビティログとauditログへの書き込みは記録しないよ。
            if (Str::startsWith($sql, ['insert into `activity_logs`', 'insert into `audits`'])) {
                return;
            }
            // フォーマット
            for ($i = 0; $i < count($query->bindings); $i++) {
                $sql = preg_replace("/\?/", is_int($query->bindings[$i]) ? $query->bindings[$i] : "'".$query->bindings[$i]."'" , $sql, 1);
            }
            // 記録
            Log::channel('querylog')->debug("(".$query->time."ms) => ".$sql);
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
