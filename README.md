MutexLock
=========

MutexLock makes sure that a cron job defined in multiple identical web servers will not execute more than one task at a time. Backed by Redis.

# sample usage

```php
use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\LineFormatter;

$log = new Logger('cron');
$syslog = new SyslogHandler('webapp');
$formatter = new LineFormatter("%channel%.%level_name%: %message%");
$syslog->setFormatter($formatter);
$log->pushHandler($syslog);

MutexLock\lock::init([
    'logger' => $log,
    'host'   => '127.0.0.1',
    'port'   => '6379',
]);

if (!MutexLock\Lock::set(LOCK_KEY, LOCK_TIME)) {
    return;
}
```
