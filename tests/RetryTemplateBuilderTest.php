<?php

namespace IlicMiljan\RetryMaster\Tests;

use IlicMiljan\RetryMaster\RetryTemplate;
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

        $retryTemplate = (new RetryTemplateBuilder())
            ->setRetryPolicy($retryPolicy)
            ->setBackoffPolicy($backoffPolicy)
            ->setRetryStatistics($retryStatistics)
            ->build();

        $this->assertInstanceOf(RetryTemplate::class, $retryTemplate);
    }
}
