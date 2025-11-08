<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class CreateHourlyLogger
{
    /**
     * Create a custom Monolog instance for hourly logging.
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('hourly');

        // Saatlik log dosyaları oluştur
        $handler = new RotatingFileHandler(
            $config['path'] . '/laravel-' . date('Y-m-d-H') . '.log',
            $config['days'] ?? 7, // 7 günlük log sakla
            $config['level'] ?? Logger::DEBUG,
            true,
            null,
            false
        );

        // Custom formatter - daha okunabilir format
        $formatter = new LineFormatter(
            "[%datetime%] %level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true,
            true
        );

        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    }
}
