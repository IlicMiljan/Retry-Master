<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

class CompositeRetryPolicy implements RetryPolicy
{
    /**
     * @var RetryPolicy[]
     */
    private array $policies;
    private bool $optimistic;

    /**
     * @param RetryPolicy[] $policies
     * @param bool $optimistic
     */
    public function __construct(array $policies, bool $optimistic = true)
    {
        $this->policies = $policies;
        $this->optimistic = $optimistic;
    }

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
