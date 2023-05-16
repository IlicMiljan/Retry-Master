<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## CompositeRetryPolicy
 *
 * CompositeRetryPolicy is an implementation of the RetryPolicy interface that
 * delegates the decision whether to retry to multiple other policies. This
 * class allows combining multiple policies in an optimistic or pessimistic
 * manner.
 *
 * In optimistic mode (the default), the operation is retried if any of the
 * policies allows it. In pessimistic mode, the operation is retried only if all
 * policies allow it.
 *
 * This class can be used in scenarios where you want to apply different rules
 * for retrying, depending on the type of exception, the number of attempts, and
 * other factors.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class CompositeRetryPolicy implements RetryPolicy
{
    /**
     * @var RetryPolicy[] Array of RetryPolicy instances to consult for retry
     *                    decision.
     */
    private array $policies;

    /**
     * @var bool Flag to indicate if the composite policy is optimistic or
     *           pessimistic.
     */
    private bool $optimistic;

    /**
     * CompositeRetryPolicy constructor.
     *
     * @param RetryPolicy[] $policies Array of RetryPolicy instances.
     * @param bool $optimistic Set to true for optimistic mode, false for
     *                         pessimistic mode.
     */
    public function __construct(array $policies, bool $optimistic = true)
    {
        $this->policies = $policies;
        $this->optimistic = $optimistic;
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
        foreach ($this->policies as $policy) {
            $shouldRetry = $policy->shouldRetry($e, $context);

            if ($this->optimistic && $shouldRetry) {
                return true;
            } elseif (!$this->optimistic && !$shouldRetry) {
                return false;
            }
        }

        return !$this->optimistic;
    }
}
