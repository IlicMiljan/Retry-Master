<?php

namespace IlicMiljan\RetryMaster;

use IlicMiljan\RetryMaster\Policy\Backoff\BackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;
use IlicMiljan\RetryMaster\Util\Sleeper;
use Psr\Log\LoggerInterface;

/**
 * Class RetryTemplateBuilder
 *
 * The RetryTemplateBuilder class provides a concrete implementation of
 * RetryTemplateBuilderInterface. It allows for the flexible construction of
 * RetryTemplate instances using the builder pattern. It offers the ability to
 * customize a RetryTemplate's retry policy, backoff policy, and retry
 * statistics and logger.
 *
 * @package IlicMiljan\RetryMaster
 */
class RetryTemplateBuilder implements RetryTemplateBuilderInterface
{
    /**
     * The retry policy for the RetryTemplate being built.
     *
     * @var RetryPolicy|null
     */
    private ?RetryPolicy $retryPolicy = null;

    /**
     * The backoff policy for the RetryTemplate being built.
     *
     * @var BackoffPolicy|null
     */
    private ?BackoffPolicy $backoffPolicy = null;

    /**
     * The retry statistics for the RetryTemplate being built.
     *
     * @var RetryStatistics|null
     */
    private ?RetryStatistics $retryStatistics = null;

    /**
     * The sleeper for the RetryTemplate being built.
     *
     * @var Sleeper|null
     */
    private ?Sleeper $sleeper = null;

    /**
     * The logger for the RetryTemplate being built.
     *
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger = null;

    /**
     * Sets the retry policy for the RetryTemplate being built.
     *
     * @param RetryPolicy $retryPolicy The retry policy to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setRetryPolicy(RetryPolicy $retryPolicy): self
    {
        $this->retryPolicy = $retryPolicy;
        return $this;
    }

    /**
     * Sets the backoff policy for the RetryTemplate being built.
     *
     * @param BackoffPolicy $backoffPolicy The backoff policy to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setBackoffPolicy(BackoffPolicy $backoffPolicy): self
    {
        $this->backoffPolicy = $backoffPolicy;
        return $this;
    }

    /**
     * Sets the retry statistics for the RetryTemplate being built.
     *
     * @param RetryStatistics $retryStatistics The retry statistics to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setRetryStatistics(RetryStatistics $retryStatistics): self
    {
        $this->retryStatistics = $retryStatistics;
        return $this;
    }

    /**
     * Sets the sleeper for the RetryTemplate being built.
     *
     * @param Sleeper $sleeper The sleeper to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setSleeper(Sleeper $sleeper): self
    {
        $this->sleeper = $sleeper;
        return $this;
    }

    /**
     * Sets the logger for the RetryTemplate being built.
     *
     * @param LoggerInterface $logger The logger to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Builds and returns a new instance of RetryTemplate.
     *
     * This method constructs a RetryTemplate using the current configuration of
     * the builder. If a policy or the statistics has not been explicitly set,
     * the RetryTemplate's default is used.
     *
     * @return RetryTemplateInterface The built RetryTemplate instance.
     */
    public function build(): RetryTemplateInterface
    {
        return new RetryTemplate(
            $this->retryPolicy,
            $this->backoffPolicy,
            $this->retryStatistics,
            $this->sleeper,
            $this->logger
        );
    }
}
