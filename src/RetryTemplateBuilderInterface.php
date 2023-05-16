<?php

namespace IlicMiljan\RetryMaster;

use IlicMiljan\RetryMaster\Policy\Backoff\BackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;
use Psr\Log\LoggerInterface;

/**
 * ## RetryTemplateBuilderInterface
 *
 * The RetryTemplateBuilderInterface provides a contract for building instances
 * of RetryTemplateInterface. It enables the flexible creation of RetryTemplate
 * instances using the builder pattern, where the different aspects of a
 * RetryTemplate (like its retry policy, backoff policy, and retry statistics)
 * can be individually configured before the RetryTemplate is built.
 *
 * @package IlicMiljan\RetryMaster
 */
interface RetryTemplateBuilderInterface
{
    /**
     * Sets the retry policy for the RetryTemplate being built.
     *
     * The retry policy determines the conditions under which a failed operation
     * should be retried.
     *
     * @param RetryPolicy $retryPolicy The retry policy to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setRetryPolicy(RetryPolicy $retryPolicy): self;

    /**
     * Sets the backoff policy for the RetryTemplate being built.
     *
     * The backoff policy determines the wait time between retry attempts.
     *
     * @param BackoffPolicy $backoffPolicy The backoff policy to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setBackoffPolicy(BackoffPolicy $backoffPolicy): self;

    /**
     * Sets the retry statistics for the RetryTemplate being built.
     *
     * The retry statistics keep track of various statistics about the retry
     * operations.
     *
     * @param RetryStatistics $retryStatistics The retry statistics to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setRetryStatistics(RetryStatistics $retryStatistics): self;

    /**
     * Sets the logger for the RetryTemplate being built.
     *
     * @param LoggerInterface $logger The logger to set.
     * @return self Returns the builder instance for method chaining.
     */
    public function setLogger(LoggerInterface $logger): self;

    /**
     * Builds and returns a new instance of RetryTemplateInterface.
     *
     * This method should use the current configuration of the builder to
     * construct the RetryTemplate.
     *
     * @return RetryTemplateInterface The built RetryTemplate instance.
     */
    public function build(): RetryTemplateInterface;
}
