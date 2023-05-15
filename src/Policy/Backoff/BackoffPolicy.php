<?php

namespace IlicMiljan\RetryMaster\Policy\Backoff;

/**
 * ## Backoff Policy
 *
 * BackoffPolicy is an interface defining a contract for Backoff policies in
 * retry operations. A Backoff policy determines a wait period that a retry
 * operation should pause for before making the next attempt after a failed
 * attempt.
 *
 * @package IlicMiljan\RetryMaster\Policy\Backoff
 */
interface BackoffPolicy
{
    /**
     * Determines the backoff period in milliseconds for a given retry attempt.
     *
     * @param int $attempt The number of the current retry attempt (1 for the
     *                     first attempt, 2 for the second attempt, and so on).
     *
     * @return int The backoff period in milliseconds. The interpretation of
     *             the returned value is up to the calling code, but typically
     *             it would pause execution for the returned number of
     *             milliseconds before making the next retry attempt.
     */
    public function backoff(int $attempt): int;
}
