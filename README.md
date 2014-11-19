# File Log Writer Component - Mendo Framework

```Mendo\Logger\Writer\FileLogWriter``` writes log information to a rolling file.

## Usage

```php
$dir = './logs';
$filename = 'logfile';
$rollTime = Mendo\Logger\Writer\FileLogWriter::ROLL_DAILY;
$rollSize = '10MB';

$writer = new Mendo\Logger\Writer\FileLogWriter($dir, $filename, $rollTime, $rollSize);

$writer->info('hello world');
```

## Installation

You can install Mendo File Log Writer using the dependency management tool [Composer](https://getcomposer.org/).
Run the *require* command to resolve and download the dependencies:

```
composer require mendoframework/logwriter-file
```