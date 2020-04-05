<?php declare(strict_types=1);

namespace Spacetab\Logger\Tests;

use Spacetab\Logger\Logger;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;
use PHPUnit\Framework\TestCase;


class LoggerTest extends TestCase
{
    public const LOGGER_TEST_PATH = '/tmp/logger-test-file.txt';

    public function testHowLoggerWorks()
    {
        if (file_exists(self::LOGGER_TEST_PATH)) {
            unlink(self::LOGGER_TEST_PATH);
        }

        $log = new Logger();
        $log->addErrorLogHandler();
        $log->addHandler(function (): HandlerInterface {
            return new class() extends AbstractHandler implements HandlerInterface {
                public function handle(array $record): bool {
                    file_put_contents(LoggerTest::LOGGER_TEST_PATH, $record['message'], FILE_APPEND);
                    return true;
                }
            };
        });
        $log->register();

        $log->getMonolog()->info('rec1');
        $log->getMonolog()->info('rec2');

        $this->assertStringEndsWith('rec1rec2', file_get_contents(LoggerTest::LOGGER_TEST_PATH));
    }

    public function testHowWorksStaticMethods()
    {
        $unique = uniqid();
        ini_set('error_log', $this->getFilename($unique));

        $log = Logger::default();
        $log->info('log 1');

        $log = Logger::new();
        $log->register();

        $log->getMonolog()->info('log 2');

        $string = file_get_contents($this->getFilename($unique));

        $matches = [];
        preg_match_all('/log\s(\d{1})/i', $string, $matches);

        $this->assertCount(2, $matches[0]);
    }

    /**
     * @param string $unique
     * @return string
     */
    private function getFilename(string $unique): string
    {
        return sys_get_temp_dir() . "/test_logger_error_log_$unique.txt";
    }
}
