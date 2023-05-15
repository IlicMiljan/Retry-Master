<?php

namespace IlicMiljan\RetryMaster\Policy\Backoff;

/**
 * ## No Backoff Policy
 *
 * NoBackoffPolicy is an implementation of the BackoffPolicy interface that
 * provides no backoff. This means that there is no wait time between retry
 * attempts, i.e., the retries occur immediately after a failure.
 *
 * This class is useful in scenarios where you want to retry an operation
 * immediately after a failure without any delay. It should be used cautiously
 * as it can potentially lead to higher load on the system in case of persistent
 * failures, due to the absence of any delay between consecutive retry attempts.
 *
 * @package IlicMiljan\RetryMaster\Policy\Backoff
 */
class NoBackoffPolicy implements BackoffPolicy
{
    /**
     * Determines the backoff period in milliseconds for a given retry attempt.
     *
     * @param int $attempt The number of the current retry attempt (1 for the
     *                     first attempt, 2 for the second attempt, and so on).
     * @return int The backoff period in milliseconds, always zero for this
     *             policy.
     */
    public function backoff(int $attempt): int
    {
        return 0;
    }
}
