<?php

namespace IlicMiljan\RetryMaster\Tests\Logger;

use IlicMiljan\RetryMaster\Logger\NullLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class NullLoggerTest extends TestCase
{
    public function testNullLogger(): void
    {
        $nullLogger = new NullLogger();

        // Define log levels as per PSR-3 standard.
        $logLevels = [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ];

        // Test that no exception is thrown when we log a message at any level.
        foreach ($logLevels as $logLevel) {
            try {
                $nullLogger->log($logLevel, 'Test message');
                $this->assertTrue(true);
            } catch (\Exception $e) {
                $this->fail('An exception was thrown when it should not have been.');
            }
        }
    }
}
