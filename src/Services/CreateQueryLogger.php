<?php
/**
 * 参考サイト
 * https://note.kiriukun.com/entry/20190824-logging-sql-queries-to-other-logfile-using-custom-channel-in-laravel
 */
namespace programming_cat\QueryLog\Services;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class CreateQueryLogger {
    public function __invoke(array $config) {
        $level = Logger::toMonologLevel($config['level']);
        $filePermission = 0666;

        if (isset($config['days']) && is_numeric($config['days']) && $config['days']>0) {
            $hander = new RotatingFileHandler($config['path'], $config['days'], $level, true, $filePermission);
        } else {
            $hander = new StreamHandler($config['path'], $level , true, $filePermission);
        }

        $hander->setFormatter(new LineFormatter(/* format */ null, /* dateFormat */ null, /* allowInlineLineBreaks */ true, /* ignoreEmptyContextAndExtra */ true));

        $logger = new Logger('SQL');
        $logger->pushHandler($hander);
        return $logger;

    }
}