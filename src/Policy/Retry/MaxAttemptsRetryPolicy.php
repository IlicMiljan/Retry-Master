<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## MaxAttemptsRetryPolicy
 *
 * MaxAttemptsRetryPolicy is an implementation of the RetryPolicy interface that
 * allows an operation to be retried a specified maximum number of times.
 *
 * This policy is useful in scenarios where you want to limit the number of
 * retry attempts for an operation to avoid excessive retries.
 *
 * If the number of retry attempts reaches the maximum limit specified, this
 * policy will prevent further retries.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class MaxAttemptsRetryPolicy implements RetryPolicy
{
    /**
     * @var int The maximum number of retry attempts.
     */
    private int $maxAttempts;

    /**
     * MaxAttemptsRetryPolicy constructor.
     *
     * @param int $maxAttempts The maximum number of retry attempts.
     */
    public function __construct(int $maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * Determines whether a retry should be performed based on the exception
     * and the context of the operation.
     *
     * @param Exception $e The exception that caused the operation to fail.
     * @param RetryContext $context The context of the operation, containing
     *                              information such as the number of attempts
     *                              so far and the last exception.
     * @return bool Returns true if the operation should be retried, false
     *              otherwise.
     */
    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        return $context->getRetryCount() <= $this->maxAttempts;
    }
}
