<?php

namespace components\Component\Slim\Log;


use components\Helper\Str;
use InvalidArgumentException;
use Monolog\Handler\SlackWebhookHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\RotatingFileHandler;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Illuminate\Contracts\Logging\Log as LogContract;


class MonologWriter implements LogContract, PsrLoggerInterface
{
    /**
     * The Monolog logger instance.
     *
     * @var \Monolog\Logger
     */
    protected $monolog;

    /**
     * @var array
     */
    protected $settings;

    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug'     => MonologLogger::DEBUG,
        'info'      => MonologLogger::INFO,
        'notice'    => MonologLogger::NOTICE,
        'warning'   => MonologLogger::WARNING,
        'error'     => MonologLogger::ERROR,
        'critical'  => MonologLogger::CRITICAL,
        'alert'     => MonologLogger::ALERT,
        'emergency' => MonologLogger::EMERGENCY,
    ];

    /**
     * Slim Log levels
     *
     * @var array
     */
    protected $slimLevels = [
        \Slim\Log::EMERGENCY    => MonologLogger::EMERGENCY,
        \Slim\Log::ALERT        => MonologLogger::ALERT,
        \Slim\Log::CRITICAL     => MonologLogger::CRITICAL,
        \Slim\Log::ERROR        => MonologLogger::ERROR,
        \Slim\Log::WARN         => MonologLogger::WARNING,
        \Slim\Log::NOTICE       => MonologLogger::NOTICE,
        \Slim\Log::INFO         => MonologLogger::INFO,
        \Slim\Log::DEBUG        => MonologLogger::DEBUG,
    ];

    /**
     * MonologWriter constructor.
     * @param array $settings
     * @param bool $merge
     */
    public function __construct($settings = [], $merge = true)
    {
        if ($merge) {
            $this->settings = array_merge($this->getDefaultSettings(), $settings);
        } else {
            $this->settings = $settings;
        }

        $this->monolog = new MonologLogger($this->settings['name']);

        $this->monolog->pushProcessor(static::logSeparatorProcessor());
        $this->monolog->pushProcessor(new UidProcessor(32));

        foreach ($this->settings['handlers'] as $handler) {
            if (!$handler instanceof \Monolog\Handler\HandlerInterface) {
                throw new \RuntimeException("handlers must be an implementation of '\Monolog\Handler\HandlerInterface'");
            }
            $this->monolog->pushHandler($handler);
        }
        foreach ($this->settings['processors'] as $processor) {
            if (!is_callable($processor)) {
                throw new \RuntimeException("processors must be callable");
            }
            $this->monolog->pushProcessor($processor);
        }
    }

    /**
     * @param string $name
     * @param bool $levels
     * @param bool $daily
     * @param bool $extra
     * @param bool $slack
     * @param string $ext
     * @return static
     */
    public static function register($name = 'default', $levels = false, $daily = false, $extra = false, $slack = false, $ext = '')
    {
        $settings = [
            'name'  => $name,
            'ext'   => $ext,
        ];

        $_instance = new static($settings);

        if ($levels !== false) {
            $_instance->levels($levels);
        }

        if ($daily !== false) {
            $_instance->daily($daily);
        }

        if ($slack !== false) {
            $_instance->slack($slack);
        }

        if ($extra !== false) {
            $_instance->withExtra();
        }

        return $_instance;
    }

    /**
     * @param $levels
     */
    public function levels($levels)
    {
        $levels = (array)$levels;

        foreach ($levels as $level) {
            $this->useFiles(
                self::logPath() . DIRECTORY_SEPARATOR . $this->settings['name'] . '-' . Str::lower($level) . ($this->settings['ext'] ? '.' . $this->settings['ext'] : ''),
                $level
            );
        }
    }

    /**
     * @param $daily
     */
    public function daily($daily)
    {
        $dailyLevel = $daily['level'] ?? 'debug';

        $this->useDailyFiles(
            self::logPath() . DIRECTORY_SEPARATOR . $this->settings['name'] . '-daily-' . Str::lower($dailyLevel) . ($this->settings['ext'] ? '.' . $this->settings['ext'] : ''),
            $daily['day'],
            $dailyLevel
        );
    }

    /**
     * @param $slack
     */
    public function slack($slack)
    {
        $this->monolog->pushHandler(new SlackWebhookHandler(
            $slack['webhook'],
            $slack['channel'],
            'slackbot',
            true,
            null,
            true,
            true,
            $this->parseLevel($slack['level']),
            true
        ));
    }

    /**
     * Extra processor
     */
    public function withExtra()
    {
        $this->monolog->pushProcessor(static::extraDataProcessor());
    }

    /**
     * @return bool|string
     */
    public static function logPath()
    {
        return (defined('LOG_DIR')
            ? LOG_DIR
            : implode(DIRECTORY_SEPARATOR, ['', 'www', 'logs', 'php']));
    }

    /**
     * @return \Closure
     */
    private static function extraDataProcessor()
    {
        return function ($record) {
            $record['extra']['postData'] = $_POST;
            $record['extra']['getData'] = $_GET;
            $record['extra']['sessionData'] = $_SESSION;
            $record['extra']['serverData'] = $_SERVER;

            return $record;
        };
    }

    /**
     * @return \Closure
     */
    private static function logSeparatorProcessor()
    {
        return function ($record) {
            $record['extra']['separator'] = ['--------------------------'];
            return $record;
        };
    }

    /**
     * @param string $name
     * @param string $message
     * @param bool $daily
     * @param bool $extra
     * @param bool $slack
     * @param string $ext
     */
    public static function manualWriteData($name = 'data', $message = 'Data', $daily = false, $extra = false, $slack = false, $ext = '')
    {
        (static::register(
            $name,
            [],
            $daily,
            $extra,// important!!!   true
            $slack,
            $ext
        ))->debug($message);
    }

    /**
     * Log an emergency message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an alert message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a critical message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an error message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a notice to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an informational message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a debug message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a message to the logs.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Dynamically pass log calls into the writer.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public function write($message, $level, array $context = [])
    {
        $level = $this->slim2writer($level);
        $this->writeLog($level, $message, $context);
    }

    /**
     * Write a message to Monolog.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    protected function writeLog($level, $message, $context)
    {
        $this->monolog->{$level}($message, $context);
    }

    /**
     * Register a file log handler.
     *
     * @param  string  $path
     * @param  string  $level
     * @return void
     */
    public function useFiles($path, $level = 'debug')
    {
        $this->monolog->pushHandler($handler = new StreamHandler($path, $this->parseLevel($level)));

        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * Register a daily file log handler.
     *
     * @param  string  $path
     * @param  int     $days
     * @param  string  $level
     * @return void
     */
    public function useDailyFiles($path, $days = 0, $level = 'debug')
    {
        $this->monolog->pushHandler(
            $handler = new RotatingFileHandler($path, $days, $this->parseLevel($level))
        );

        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * Register a Syslog handler.
     *
     * @param  string  $name
     * @param  string  $level
     * @param  mixed  $facility
     * @return \Psr\Log\LoggerInterface
     */
    public function useSyslog($name = 'laravel', $level = 'debug', $facility = LOG_USER)
    {
        return $this->monolog->pushHandler(
            new SyslogHandler($name, $facility, $this->parseLevel($level))
        );
    }

    /**
     * Register an error_log handler.
     *
     * @param  string  $level
     * @param  int  $messageType
     * @return void
     */
    public function useErrorLog($level = 'debug', $messageType = ErrorLogHandler::OPERATING_SYSTEM)
    {
        $this->monolog->pushHandler(
            new ErrorLogHandler($messageType, $this->parseLevel($level))
        );
    }

    /**
     * @param string $level
     * @throws \Exception
     */
    public function useCli($level = 'debug')
    {
        $this->monolog->pushHandler($handler = new StreamHandler('php://stdout', $this->parseLevel($level)));

        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * Format the parameters for the logger.
     *
     * @param  mixed  $message
     * @return mixed
     */
    protected function formatMessage($message)
    {
        if (is_array($message)) {
            return var_export($message, true);
        } elseif ($message instanceof Jsonable) {
            return $message->toJson();
        } elseif ($message instanceof Arrayable) {
            return var_export($message->toArray(), true);
        }

        return $message;
    }

    /**
     * Parse the string level into a Monolog constant.
     *
     * @param  string  $level
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public function parseLevel($level)
    {
        if (isset($this->levels[$level])) {
            return $this->levels[$level];
        }

        throw new InvalidArgumentException('Invalid log level.');
    }

    /**
     * Get the underlying Monolog instance.
     *
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->monolog;
    }

    /**
     * Get a default Monolog formatter instance.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function getDefaultFormatter()
    {
        $formatter = new LineFormatter(null, null, true, true);
        $formatter->includeStacktraces();
        return $formatter;
    }

    /**
     * @return array
     */
    protected function getDefaultSettings()
    {
        return [
            'name' => 'SlimMonoLogger',
            'ext' => '',
            'handlers' => [],
            'processors' => []
        ];
    }

    /**
     * Parse the int Slim log level into a Monolog constant.
     *
     * @param  int  $level
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function slim2writer($level)
    {
        if (($writerLevel = array_search($this->slimLevels[$level], $this->levels)) !== false) {
            return $writerLevel;
        }

        throw new InvalidArgumentException('Invalid log level.');
    }
}