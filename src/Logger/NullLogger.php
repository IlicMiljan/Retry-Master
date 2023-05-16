<?php

namespace IlicMiljan\RetryMaster\Logger;

use Psr\Log\LoggerInterface;

/**
 * This Logger ignores all messages.
 */
class NullLogger implements LoggerInterface
{
    public function emergency($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function alert($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function critical($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function error($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function warning($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function notice($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function info($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function debug($message, array $context = array()): void
    {
        // Do nothing.
    }

    public function log($level, $message, array $context = array()): void
    {
        // Do nothing.
    }
}
