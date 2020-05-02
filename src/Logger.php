<?php declare(strict_types=1);

namespace Spacetab\Logger;

use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\ByteStream;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class Logger
{
    public const CHANNEL = 'App';

    /**
     * Pre-defined logger handlers.
     *
     * @var array<callable>
     */
    private array $handlers = [];

    /**
     * @var string
     */
    private string $channel;

    /**
     * @var Monolog
     */
    private Monolog $monolog;

    /**
     * @var string
     */
    private string $level;

    /**
     * Logger constructor.
     *
     * @param string $channel
     * @param string $level
     */
    public function __construct(string $channel = self::CHANNEL, string $level = LogLevel::INFO)
    {
        $this->channel = $channel;
        $this->level   = $level;

        $this->monolog = new Monolog($channel);
    }

    /**
     * Return default and most-usable logger instance.
     *
     * @param string $channel
     * @param string $level
     * @return Logger
     */
    public static function new(string $channel = self::CHANNEL, string $level = LogLevel::INFO): Logger
    {
        $log = new Logger($channel, $level);
        $log->addStreamHandler();

        return $log;
    }

    /**
     * Return registered default logger.
     *
     * @param string $channel
     * @param string $level
     * @return \Psr\Log\LoggerInterface
     */
    public static function default(string $channel = self::CHANNEL, string $level = LogLevel::INFO): LoggerInterface
    {
        $log = new Logger($channel, $level);
        $log->addStreamHandler();
        $log->register();

        return $log->getMonolog();
    }

    /**
     * Add Monolog handler use callback.
     *
     * @param callable $callback
     * @return Logger
     */
    public function addHandler(callable $callback): self
    {
        $this->handlers[] = $callback;

        return $this;
    }

    /**
     * Register all handlers.
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->handlers as $handler) {
            $this->monolog->pushHandler($handler($this->level));
        }
    }

    /**
     * Create Monolog logger without fucking brackets -> [] []  [] []  [] []  [] []  [] []
     * if context and extra is empty.
     */
    public function addStreamHandler(): void
    {
        $this->addHandler(function (string $level) {
            $formatter = new ConsoleFormatter();
            $formatter->ignoreEmptyContextAndExtra();

            $handler = new StreamHandler(ByteStream\getStdout(), $level);
            $handler->setFormatter($formatter);

            return $handler;
        });
    }

    /**
     * @return Monolog
     */
    public function getMonolog(): Monolog
    {
        return $this->monolog;
    }
}
