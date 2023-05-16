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
     * Executes the given RetryCallback, handling retries according to the
     * configured retry and backoff policies.
     *
     * This method will continue to retry the operation until the retry policy
     * determines that a retry should not
     * be attempted, at which point the last exception thrown by the operation
     * will be rethrown.
     *
     * @param RetryCallback $retryCallback The operation to retry.
     * @throws Exception If the operation fails and the retry policy determines
     *                   that a retry should not be attempted.
     * @return mixed The result of the operation.
     */
    public function execute(RetryCallback $retryCallback)
    {
        $context = new RetryContext();

        while (true) {
            $context->incrementRetryCount();
            $this->retryStatistics->incrementTotalAttempts();

            try {
                $result = $retryCallback->doWithRetry($context);

                $this->retryStatistics->incrementSuccessfulAttempts();

                $this->logger->info('Operation succeeded on attempt', [
                    'attempt' => $context->getRetryCount(),
                    'successfulAttempts' => $this->retryStatistics->getSuccessfulAttempts(),
                    'totalAttempts' => $this->retryStatistics->getTotalAttempts()
                ]);

                return $result;
            } catch (Exception $e) {
                $this->retryStatistics->incrementFailedAttempts();

                $this->logger->error('Operation failed', [
                    'exception' => $e,
                    'failedAttempts' => $this->retryStatistics->getFailedAttempts(),
                    'totalAttempts' => $this->retryStatistics->getTotalAttempts()
                ]);

                if (!$this->retryPolicy->shouldRetry($e, $context)) {
                    throw $e;
                }

                $context->setLastException($e);

                $sleepTime = $this->backoffPolicy->backoff($context->getRetryCount());
                $this->retryStatistics->incrementSleepTime($sleepTime);

                $this->logger->info('Sleeping before next attempt', [
                    'sleepTime' => $sleepTime,
                    'totalSleepTime' => $this->retryStatistics->getTotalSleepTimeMilliseconds()
                ]);

                Sleep::milliseconds($sleepTime);
            }
        }
    }

    /**
     * Executes the given RetryCallback, handling retries according to the
     * configured retry and backoff policies. If all retry attempts fail,
     * this method will execute the provided RecoveryCallback.
     *
     * This method will continue to retry the operation until the retry policy
     * determines that a retry should not be attempted, at which point it will
     * execute the RecoveryCallback and return its result instead of throwing
     * an exception.
     *
     * @param RetryCallback $retryCallback The operation to retry.
     * @param RecoveryCallback $recoveryCallback The recovery operation to
     *                                           execute if all retries fail.
     * @throws Exception If the recovery operation itself throws an exception.
     * @return mixed The result of the operation or the result of the recovery
     *               operation if all retries fail.
     */
    public function executeWithRecovery(RetryCallback $retryCallback, RecoveryCallback $recoveryCallback)
    {
        $context = new RetryContext();

        while (true) {
            $context->incrementRetryCount();
            $this->retryStatistics->incrementTotalAttempts();

            try {
                $result = $retryCallback->doWithRetry($context);

                $this->retryStatistics->incrementSuccessfulAttempts();

                $this->logger->info('Operation succeeded on attempt', [
                    'attempt' => $context->getRetryCount(),
                    'successfulAttempts' => $this->retryStatistics->getSuccessfulAttempts(),
                    'totalAttempts' => $this->retryStatistics->getTotalAttempts()
                ]);

                return $result;
            } catch (Exception $e) {
                $this->retryStatistics->incrementFailedAttempts();

                $this->logger->error('Operation failed', [
                    'exception' => $e,
                    'failedAttempts' => $this->retryStatistics->getFailedAttempts(),
                    'totalAttempts' => $this->retryStatistics->getTotalAttempts()
                ]);

                if (!$this->retryPolicy->shouldRetry($e, $context)) {
                    return $recoveryCallback->recover($context);
                }

                $context->setLastException($e);

                $sleepTime = $this->backoffPolicy->backoff($context->getRetryCount());
                $this->retryStatistics->incrementSleepTime($sleepTime);

                $this->logger->info('Sleeping before next attempt', [
                    'sleepTime' => $sleepTime,
                    'totalSleepTime' => $this->retryStatistics->getTotalSleepTimeMilliseconds()
                ]);

                Sleep::milliseconds($sleepTime);
            }
        }
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
