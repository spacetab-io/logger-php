Simple pre-configured Monolog wrapper
-------------------------------------

[![CircleCI](https://circleci.com/gh/spacetab-io/logger-php.svg?style=svg)](https://circleci.com/gh/spacetab-io/logger-php)
[![codecov](https://codecov.io/gh/spacetab-io/logger-php/branch/master/graph/badge.svg)](https://codecov.io/gh/spacetab-io/logger-php)

Since `2.0.0` logger is non-blocking and based on Amp and Monolog. 

I create this library with one target, — I'm sick of always copies 
and paste same code to any of my microservices with logs. 

This wrapper solves one problem with logs. And this problem, — fucking brackets -> [] [] [] [].

Example, how this wrapper present the logs:

```bash
[2019-04-29 21:39:52] Server.INFO: CONFIG_PATH = ./configuration  
[2019-04-29 21:39:52] Server.INFO: STAGE = local  
[2019-04-29 21:39:52] Server.INFO: Configuration module loaded  
[2019-04-29 21:39:52] Server.INFO: HTTP static server started at 0.0.0.0:8080  
 
```

This is a normal view and without fucking brackets of Monolog's. Okay, I'm hate it too, how it uses?

## Usage

0) Install library through `composer`:

```bash
composer require spacetab-io/logger
```

1) Basic example for most cases will do like as:

```php
use Spacetab\Logger\Logger;

$log = Logger::default(); // Psr\Log\LoggerInterface
$log->info('write something');
```

2) If you want register this library thought Service Provider (add to application DI container), see this example:

Note: this is a pseudocode for example.

```php
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerModule implements ServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public function provideServices($container): void
    {
        $container->register(LoggerInterface::class, function () {
            return Logger::default(); // it's all!
        });
    }
}
```

3) How to register logger without static:: method?
It also very simple.

```php
<?php

use Spacetab\Logger\Logger;
use Psr\Log\LogLevel;

$log = new Logger('ChannelName', LogLevel::DEBUG); // enabled debug mode and set the channel name
$log->addStreamHandler();
$log->register();

$log->getMonolog()->info('Hello world');
```

4) Okay. It's cool. But I have written logs to multiple streams. How I do it?

```php
<?php
use Spacetab\Logger\Logger;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Psr\Log\LogLevel;

$log = new Logger('ChannelName', LogLevel::DEBUG); // enabled debug mode and set the channel name
$log->addStreamHandler();
$log->addHandler(function (string $level): HandlerInterface {
    return new NullHandler($level);
});

$log->register();

$log->getMonolog()->info('Diligence is the mother of success');
```

Simple? Yes, and without fucking brackets.

## License

The MIT License

Copyright © 2022 spacetab.io, Inc. https://spacetab.io

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
