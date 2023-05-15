<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

class NonRepeatingExceptionRetryPolicy implements RetryPolicy
{
    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        $lastException = $context->getLastException();

        // If there was no previous exception or the previous exception is of a different type, retry.
        if ($lastException === null || get_class($lastException) !== get_class($e)) {
            return true;
        }

        // If the last exception is of the same type as the current exception, don't retry.
        return false;
    }
}
