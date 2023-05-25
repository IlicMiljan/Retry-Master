<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\MaxAttemptsRetryPolicy;
use PHPUnit\Framework\TestCase;

class MaxAttemptsRetryPolicyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $exception = new Exception();

        $retryContext = $this->createMock(RetryContext::class);
        $retryContext->method('getRetryCount')->willReturnOnConsecutiveCalls(1, 2, 3, 4, 5);

        $maxAttempts = 3;
        $retryPolicy = new MaxAttemptsRetryPolicy($maxAttempts);

        // Should retry until the number of attempts reaches the maximum limit
        $this->assertTrue($retryPolicy->shouldRetry($exception, $retryContext));
        $this->assertTrue($retryPolicy->shouldRetry($exception, $retryContext));
        $this->assertTrue($retryPolicy->shouldRetry($exception, $retryContext));

        // Should not retry when the number of attempts has reached the maximum limit
        $this->assertFalse($retryPolicy->shouldRetry($exception, $retryContext));
        $this->assertFalse($retryPolicy->shouldRetry($exception, $retryContext));
    }
}
