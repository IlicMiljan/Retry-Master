<?php

namespace IlicMiljan\RetryMaster;

use Exception;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Backoff\BackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Backoff\FixedBackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\MaxAttemptsRetryPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use IlicMiljan\RetryMaster\Statistics\InMemoryRetryStatistics;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;
use IlicMiljan\RetryMaster\Util\Sleep;

class RetryTemplate
{
    public const DEFAULT_MAX_ATTEMPTS = 3;

    private RetryPolicy $retryPolicy;
    private BackoffPolicy $backoffPolicy;
    private RetryStatistics $retryStatistics;

    public function __construct(
        RetryPolicy $retryPolicy = null,
        BackoffPolicy $backoffPolicy = null,
        RetryStatistics $retryStatistics = null
    ) {
        $this->retryPolicy = $retryPolicy ?: new MaxAttemptsRetryPolicy(self::DEFAULT_MAX_ATTEMPTS);
        $this->backoffPolicy = $backoffPolicy ?: new FixedBackoffPolicy();
        $this->retryStatistics = $retryStatistics ?: new InMemoryRetryStatistics();
    }

    /**
     * @throws Exception
     * @return mixed
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

                return $result;
            } catch (Exception $e) {
                $this->retryStatistics->incrementFailedAttempts();

                if (!$this->retryPolicy->shouldRetry($e, $context)) {
                    throw $e;
                }

                $context->setLastException($e);

                $sleepTime = $this->backoffPolicy->backoff($context->getRetryCount());
                $this->retryStatistics->incrementSleepTime($sleepTime);

                Sleep::milliseconds($sleepTime);
            }
        }
    }

    public function getRetryStatistics(): RetryStatistics
    {
        return $this->retryStatistics;
    }
}
