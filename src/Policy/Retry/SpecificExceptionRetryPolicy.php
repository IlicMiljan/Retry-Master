<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

class SpecificExceptionRetryPolicy implements RetryPolicy
{
    private string $exceptionClass;

    public function __construct(string $exceptionClass)
    {
        $this->exceptionClass = $exceptionClass;
    }

    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        return $e instanceof $this->exceptionClass;
    }
}
