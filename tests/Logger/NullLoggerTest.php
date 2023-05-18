<?php

namespace IlicMiljan\RetryMaster\Tests\Logger;

use IlicMiljan\RetryMaster\Logger\NullLogger;
use PHPUnit\Framework\TestCase;

class NullLoggerTest extends TestCase
{
    public function testNullLogger(): void
    {
        $logger = new NullLogger();
        $message = 'Test message';
        $context = ['key' => 'value'];

        // Call each logging method and ensure no errors occur
        $logger->emergency($message, $context);
        $logger->alert($message, $context);
        $logger->critical($message, $context);
        $logger->error($message, $context);
        $logger->warning($message, $context);
        $logger->notice($message, $context);
        $logger->info($message, $context);
        $logger->debug($message, $context);
        $logger->log('custom', $message, $context);

        $this->assertTrue(true);
    }
}
