<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\NeverRetryPolicy;
use PHPUnit\Framework\TestCase;

class NeverRetryPolicyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);

        $retryPolicy = new NeverRetryPolicy();

        // NeverRetryPolicy should always return false for shouldRetry
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }
}
