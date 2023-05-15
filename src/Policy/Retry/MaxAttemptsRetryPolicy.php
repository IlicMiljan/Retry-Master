<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

class MaxAttemptsRetryPolicy implements RetryPolicy
{
    private int $maxAttempts;

    public function __construct(int $maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }

    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        return $context->getRetryCount() < $this->maxAttempts;
    }
}
