<?php

namespace IlicMiljan\RetryMaster;

use IlicMiljan\RetryMaster\Policy\Backoff\BackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;

/**
 * Class RetryTemplateBuilder
 *
 * The RetryTemplateBuilder class provides a concrete implementation of
 * RetryTemplateBuilderInterface. It allows for the flexible construction of
 * RetryTemplate instances using the builder pattern. It offers the ability to
 * customize a RetryTemplate's retry policy, backoff policy, and retry statistics.
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
            $this->retryStatistics
        );
    }
}
