<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

class SimpleRetryPolicy implements RetryPolicy
{
    private int $maxAttempts;
    /**
     * @var Exception[]
     */
    private array $retryableExceptions;

    /**
     * @param int $maxAttempts
     * @param Exception[] $retryableExceptions
     */
    public function __construct(int $maxAttempts = 3, array $retryableExceptions = [])
    {
        $this->maxAttempts = $maxAttempts;
        $this->retryableExceptions = $retryableExceptions;
    }

    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        if ($context->getRetryCount() >= $this->maxAttempts) {
            return false;
        }

        // If no specific exceptions are defined, retry on all exceptions.
        if (empty($this->retryableExceptions)) {
            return true;
        }

        foreach ($this->retryableExceptions as $retryableException) {
            // Retry if the exception is an instance of a retryable exception.
            if ($e instanceof $retryableException) {
                return true;
            }
        }

        return false;
    }
}
