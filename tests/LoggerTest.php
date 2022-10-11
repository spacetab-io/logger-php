<?php

declare(strict_types=1);

namespace Spacetab\Logger\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Spacetab\Logger\Logger;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;

class LoggerTest extends TestCase
{
    public function testHowLoggerWorks(): void
    {
        global $globalMustDieOrNot;

        $log = new Logger();
        $log->addStreamHandler();
        $log->addHandler(function (): HandlerInterface {
            return new class() extends AbstractHandler {
                public function handle(array $record): bool {
                    global $globalMustDieOrNot;
                    $globalMustDieOrNot[] = $record;
                    return true;
                }
            };
        });
        $log->register();

        $log->getMonolog()->info('rec1');
        $log->getMonolog()->info('rec2');

        foreach (['rec1', 'rec2'] as $index => $value) {
            $this->assertSame($value, $globalMustDieOrNot[$index]['message']);
        }
    }

    public function testHowWorksStaticMethods(): void
    {
        $log = Logger::default();

        $this->assertInstanceOf(\Monolog\Logger::class, $log);
        $this->assertInstanceOf(LoggerInterface::class, $log);

        $log->info('log 1');

        $log = Logger::new();
        $this->assertInstanceOf(Logger::class, $log);

        $log = Logger::stderr();

        $this->assertInstanceOf(\Monolog\Logger::class, $log);
        $this->assertInstanceOf(LoggerInterface::class, $log);
    }
}
