# programming_cat/querylog

<p align="center">
<a href="https://packagist.org/packages/programming_cat/querylog"><img src="https://img.shields.io/packagist/v/programming_cat/querylog.svg?style=flat-square&label=stable" alt="Packagist Stable Version"></a>
<a href="https://packagist.org/packages/programming_cat/querylog"><img src="https://img.shields.io/packagist/dt/programming_cat/querylog.svg?style=flat-square" alt="Packagist downloads"></a>
<a href="LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square" alt="MIT Software License"></a>
</p>

## About QueryLog

Laravel has no query log ecosystem. why?

## Install

```
composer require programming_cat/querylog
```
that's all you should do.

## How to use

Once you installed programming_cat/querylog your sql query would have been recorded into "storage/logs/query-yyyy-mm-dd.log".

### Logging into query log

```
\Log::channel('querylog')->debug("QUERY!");
```

## Configure

### default values

|key|value|
|---|---|
|driver|"custom"|
|via| \programming_cat\QueryLog\Services\CreateQueryLogger::class|
|path| storage_path('logs/query.log')|
|level|debug|
|days|30|
|excludes| **none** |

if config keys are exists in config/logging.php these variables are marged with existing variables.
likely..
```
array_merge($packaged_config_values, $existing_config_values);
```

### logging.channels.querylog.driver

"driver" must be "custom". 

### logging.channels.querylog.via

"via" must be "programming_cat\QueryLog\Services\CreateQueryLogger"

### logging.channels.querylog.path

define log path as you like.
default variable is storage_path('logs/query.log').

### logging.channels.querylog.level

log level.
see [monolog imprementation](https://github.com/Seldaek/monolog/blob/master/src/Monolog/Logger.php#L95)

### logging.channels.querylog.days

set rotation days. default is 30.

### logging.channels.querylog.excludes

regexp definition string or function returns boolean.

*string*
```
  'excludes' => '^(insert|update) '
```
*function*
```
  'excludes' => function (string $query) {
    return strpos($query, 'exlude-table-name') !== FALSE;
  }
```

