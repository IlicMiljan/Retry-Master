<?php

namespace IlicMiljan\RetryMaster\Tests;

use IlicMiljan\RetryMaster\Logger\NullLogger;
use IlicMiljan\RetryMaster\RetryTemplate;
use IlicMiljan\RetryMaster\Util\NanoSleeper;
use PHPUnit\Framework\TestCase;
use IlicMiljan\RetryMaster\RetryTemplateBuilder;
use IlicMiljan\RetryMaster\Policy\Retry\MaxAttemptsRetryPolicy;
use IlicMiljan\RetryMaster\Policy\Backoff\UniformRandomBackoffPolicy;
use IlicMiljan\RetryMaster\Statistics\InMemoryRetryStatistics;

class RetryTemplateBuilderTest extends TestCase
{
    public function testRetryTemplateBuilderBuildsRetryTemplate(): void
    {
        $retryPolicy = new MaxAttemptsRetryPolicy(5);
        $backoffPolicy = new UniformRandomBackoffPolicy(500, 1500);
        $retryStatistics = new InMemoryRetryStatistics();
        $sleeper = new NanoSleeper();
        $logger = new NullLogger();

        $retryTemplate = (new RetryTemplateBuilder())
            ->setRetryPolicy($retryPolicy)
            ->setBackoffPolicy($backoffPolicy)
            ->setRetryStatistics($retryStatistics)
            ->setSleeper($sleeper)
            ->setLogger($logger)
            ->build();

        $this->assertInstanceOf(RetryTemplate::class, $retryTemplate);
    }
}
