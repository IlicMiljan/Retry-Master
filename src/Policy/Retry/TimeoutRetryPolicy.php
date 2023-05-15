<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

class TimeoutRetryPolicy implements RetryPolicy
{
    private int $timeoutMilliseconds;

    public function __construct(int $timeoutMilliseconds)
    {
        $this->timeoutMilliseconds = $timeoutMilliseconds;
    }

    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        $elapsedTime = (microtime(true) - $context->getStartTime()) * 1000;

        return $elapsedTime < $this->timeoutMilliseconds;
    }
}
