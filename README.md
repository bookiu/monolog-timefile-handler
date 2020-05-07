# Monolog timefile handler

# Installation

`monolog-timefile-handler` is available via composer. Just add the following code to your `composer.json` file under **required** section and execute command `composer update` or you can run:

```bash
composer require yaxin/monolog-timefile-handler
```

# Usage

The handler needs some parameters:

- **\$filename**: This specify the log file name which can specify datetime within `$(datetime_format)`, and the datetime format accepted by php [date()](http://php.net/manual/en/function.date.php) function.

- **\$level**: An integer type defined by Monolog log level, default is `100` which means `DEBUG`.

- **\$bubble**: Whether the messages that are handled can bubble up the stack or not, default is `true`.

- **\$filePermission**: Optional file permissions (default (0644) are only for owner read/write).

- **\$useLocking**: Try to lock log file before doing any writes, default is `false`.

# Examples

```php
use Monolog\Logger;
use Yaxin\TimefileHandler\TimefileHandler;

$logger = new Logger('app');
$handler = new TimefileHandler('/tmp/app_%(Ymd_H).log', Logger::INFO);
$logger->pushHandler($handler);
$logger->warning('This is a message');
```

Then you can get the log message in `/tmp/app_<datatime>.log` file.

# License

This tool is free software and is distributed under the MIT license. Please have a look at the LICENSE file for further information.
