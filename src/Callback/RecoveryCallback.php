<?php

namespace IlicMiljan\RetryMaster\Callback;

use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * Interface RecoveryCallback
 *
 * RecoveryCallback is an interface to be implemented by classes that define a
 * recovery operation to be executed when all attempts of a retry operation have
 * failed.
 *
 * The recovery operation is defined in the recover method, which takes a
 * RetryContext parameter and returns a result. This context provides
 * information about the failed retry operation, such as the number of attempts
 * and the last exception that caused the operation to fail.
 *
 * The return type of the recover method is indicated by the return type hint of
 * 'mixed', allowing the RecoveryCallback to be used for a wide variety of
 * recovery operations.
 *
 * @package IlicMiljan\RetryMaster\Callback
 */
interface RecoveryCallback
{
    /**
     * Executes the recovery operation when all attempts of a retry operation
     * have failed.
     *
     * @param RetryContext $context The context of the failed retry operation,
     *                              containing information such as the number of
     *                              attempts and the last exception.
     * @return mixed The result of the recovery operation.
     */
    public function recover(RetryContext $context);
}
