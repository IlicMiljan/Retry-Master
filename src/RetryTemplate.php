<?php

namespace IlicMiljan\RetryMaster;

use Exception;
use IlicMiljan\RetryMaster\Callback\RecoveryCallback;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Logger\NullLogger;
use IlicMiljan\RetryMaster\Policy\Backoff\BackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Backoff\FixedBackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\MaxAttemptsRetryPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use IlicMiljan\RetryMaster\Statistics\InMemoryRetryStatistics;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;
use IlicMiljan\RetryMaster\Util\Sleep;
use Psr\Log\LoggerInterface;

/**
 * Class RetryTemplate
 *
 * The RetryTemplate class is the central component of the RetryMaster library.
 * It handles the execution of operations that may fail, applying a retry policy
 * and a backoff policy to manage retries. It also collects statistics about
 * retry attempts.
 *
 * This class is designed to be flexible and extensible. The retry and backoff
 * policies can be customized by passing in implementations of the RetryPolicy
 * and BackoffPolicy interfaces, respectively. By default, it uses a max
 * attempts retry policy with a limit of 3 attempts and a fixed backoff policy.
 *
 * @package IlicMiljan\RetryMaster
 */
class RetryTemplate implements RetryTemplateInterface
{
    /**
     * The default maximum number of retry attempts.
     */
    public const DEFAULT_MAX_ATTEMPTS = 3;

    private RetryPolicy $retryPolicy;
    private BackoffPolicy $backoffPolicy;
    private RetryStatistics $retryStatistics;
    private LoggerInterface $logger;

    public function __construct(
        RetryPolicy $retryPolicy = null,
        BackoffPolicy $backoffPolicy = null,
        RetryStatistics $retryStatistics = null,
        LoggerInterface $logger = null
    ) {
        $this->retryPolicy = $retryPolicy ?: new MaxAttemptsRetryPolicy(self::DEFAULT_MAX_ATTEMPTS);
        $this->backoffPolicy = $backoffPolicy ?: new FixedBackoffPolicy();
        $this->retryStatistics = $retryStatistics ?: new InMemoryRetryStatistics();
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Executes the operation encapsulated by the retry callback.
     *
     * @param RetryCallback $retryCallback The retry callback.
     * @return mixed The result of the retry callback execution.
     * @throws Exception If all retries fail and there's no recovery callback.
     */
    public function execute(RetryCallback $retryCallback)
    {
        return $this->doExecute($retryCallback);
    }

    /**
     * Executes the operation encapsulated by the retry callback with a recovery
     * callback.
     *
     * @param RetryCallback $retryCallback The retry callback.
     * @param RecoveryCallback $recoveryCallback The recovery callback.
     * @return mixed The result of the retry callback execution or the recovery
     *               callback if all retries fail.
     * @throws Exception If all retries fail and the recovery callback is not
     *                   provided or also fails.
     */
    public function executeWithRecovery(RetryCallback $retryCallback, RecoveryCallback $recoveryCallback)
    {
        return $this->doExecute($retryCallback, $recoveryCallback);
    }

    /**
     * Executes the operation with retry and backoff logic.
     *
     * @param RetryCallback $retryCallback The retry callback.
     * @param RecoveryCallback|null $recoveryCallback The optional recovery
     *                                                callback.
     * @return mixed The result of the retry callback execution or the recovery
     *               callback if all retries fail.
     * @throws Exception If all retries fail and the recovery callback is not
     *                   provided or also fails.
     */
    private function doExecute(RetryCallback $retryCallback, RecoveryCallback $recoveryCallback = null)
    {
        $context = new RetryContext();

        while (true) {
            $this->retryStatistics->incrementTotalAttempts();

            try {
                return $this->performOperation($retryCallback, $context);
            } catch (Exception $e) {
                $this->handleFailure($e, $context);

                if (!$this->retryPolicy->shouldRetry($e, $context)) {
                    if ($recoveryCallback) {
                        return $this->performRecovery($recoveryCallback, $context);
                    }

                    throw $e;
                }

                $this->performBackoff($context);
            }
        }
    }

    /**
     * Performs the operation encapsulated by the retry callback and increments
     * the successful attempt counter.
     *
     * @param RetryCallback $retryCallback The retry callback.
     * @param RetryContext $context The retry context.
     * @return mixed The result of the retry callback execution.
     */
    private function performOperation(RetryCallback $retryCallback, RetryContext $context)
    {
        $result = $retryCallback->doWithRetry($context);

        $this->retryStatistics->incrementSuccessfulAttempts();

        $this->logger->info('Operation succeeded on attempt', [
            'attempt' => $context->getRetryCount(),
            'successfulAttempts' => $this->retryStatistics->getSuccessfulAttempts(),
            'totalAttempts' => $this->retryStatistics->getTotalAttempts()
        ]);

        return $result;
    }

    /**
     * Performs the operation encapsulated by the recovery callback.
     *
     * @param RecoveryCallback $recoveryCallback
     * @param RetryContext $context
     * @return mixed
     * @throws Exception
     */
    private function performRecovery(RecoveryCallback $recoveryCallback, RetryContext $context)
    {
        try {
            $result = $recoveryCallback->recover($context);

            $this->logger->info('Recovery operation succeeded');

            return $result;
        } catch (Exception $e) {
            $this->logger->error('Recovery failed', [
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Handles a failure of the retry callback operation by logging the failure,
     * incrementing the failed attempt counter and updating the retry context.
     *
     * @param Exception $e The exception that was thrown.
     * @param RetryContext $context The retry context.
     */
    private function handleFailure(Exception $e, RetryContext $context): void
    {
        $this->retryStatistics->incrementFailedAttempts();

        $this->logger->error('Operation failed', [
            'exception' => $e,
            'failedAttempts' => $this->retryStatistics->getFailedAttempts(),
            'totalAttempts' => $this->retryStatistics->getTotalAttempts()
        ]);

        $context->incrementRetryCount();
        $context->setLastException($e);
    }

    /**
     * Performs the backoff policy to pause execution before a retry.
     *
     * @param RetryContext $context The retry context.
     */
    private function performBackoff(RetryContext $context): void
    {
        $sleepTime = $this->backoffPolicy->backoff($context->getRetryCount());
        $this->retryStatistics->incrementSleepTime($sleepTime);

        $this->logger->info('Sleeping before next attempt', [
            'sleepTime' => $sleepTime,
            'totalSleepTime' => $this->retryStatistics->getTotalSleepTimeMilliseconds()
        ]);

        Sleep::milliseconds($sleepTime);
    }

    /**
     * Returns the RetryStatistics instance being used by this RetryTemplate.
     *
     * @return RetryStatistics The current RetryStatistics instance.
     */
    public function getRetryStatistics(): RetryStatistics
    {
        return $this->retryStatistics;
    }
}
