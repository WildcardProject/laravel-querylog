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
		// 引数の $config には、config/logging.php で sqlQueryLog に設定した path とか days とかが入ってる！

		// 'debug' とかの文字列をMonologが使えるログレベルに変換
		$level = Logger::toMonologLevel($config['level']);

		//ログファイルのパーミッションを変更できるので、必要な場合は入れておく
		$filePermission = 0777;

		// 日ごとにログローテートするハンドラ作成
		if (isset($config['days']) && is_numeric($config['days'])) {
			$hander = new RotatingFileHandler($config['path'], $config['days'], $level, true, $filePermission);
		} else {
			$hander = new StreamHandler($config['path'], $level , true, $filePermission);
		}

		// 改行コードを出力する＆カラのコンテキストを出力しないフォーマッタを設定
		$hander->setFormatter(new LineFormatter(null, null, true, true));

		// Monologインスタンス作成してハンドラ設定して返却
		$logger = new Logger('SQL');  // ロガー名は 'SQL' にした。これはログに出力される
		$logger->pushHandler($hander);
		return $logger;

	}
}