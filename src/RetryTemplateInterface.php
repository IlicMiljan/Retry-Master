<?php

namespace IlicMiljan\RetryMaster;

use Exception;
use IlicMiljan\RetryMaster\Callback\RecoveryCallback;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;

/**
 * ## RetryTemplateInterface
 *
 * The RetryTemplateInterface provides a contract for executing operations that
 * may fail, with a certain policy for retrying upon failure. It also provides
 * a method to retrieve the statistics about retries.
 *
 * This interface is designed to support different types of retry mechanisms and
 * to make testing of retry logic easier by allowing to mock the retry template
 * in tests.
 *
 * @package IlicMiljan\RetryMaster
 */
interface RetryTemplateInterface
{
    /**
     * Executes the provided RetryCallback, handling retries according to a
     * certain retry policy.
     *
     * Implementations should continue to retry the operation until the retry
     * policy determines that a retry should not be attempted, at which point
     * the last exception thrown by the operation will be rethrown.
     *
     * @param RetryCallback $retryCallback The operation to retry.
     * @throws Exception If the operation fails and the retry policy determines
     *                   that a retry should not be attempted.
     * @return mixed The result of the operation.
     */
    public function execute(RetryCallback $retryCallback);

    /**
     * Executes the provided RetryCallback, handling retries according to a
     * certain retry policy. If all retry attempts fail, this method will
     * execute the provided RecoveryCallback.
     *
     * Implementations should continue to retry the operation until the retry
     * policy determines that a retry should not be attempted, at which point
     * it will execute the RecoveryCallback and return its result instead of
     * throwing an exception.
     *
     * @param RetryCallback $retryCallback The operation to retry.
     * @param RecoveryCallback $recoveryCallback The recovery operation to
     *                                           execute if all retries fail.
     * @throws Exception If the recovery operation itself throws an exception.
     * @return mixed The result of the operation or the result of the recovery
     *               operation if all retries fail.
     */
    public function executeWithRecovery(RetryCallback $retryCallback, RecoveryCallback $recoveryCallback);

    /**
     * Returns the RetryStatistics instance being used by this RetryTemplate.
     *
     * Implementations should return the current RetryStatistics instance which
     * holds statistical data about the retry attempts.
     *
     * @return RetryStatistics The current RetryStatistics instance.
     */
    public function getRetryStatistics(): RetryStatistics;
}
