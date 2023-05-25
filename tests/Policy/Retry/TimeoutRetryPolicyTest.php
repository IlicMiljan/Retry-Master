<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\TimeoutRetryPolicy;
use PHPUnit\Framework\TestCase;

class TimeoutRetryPolicyTest extends TestCase
{
    public function testShouldRetryWithinTimeout(): void
    {
        $exception = new Exception("Exception");
        $context = $this->createMock(RetryContext::class);

        $context->method('getStartTime')
            ->willReturn(microtime(true) - 0.5);  // 500 milliseconds ago

        $retryPolicy = new TimeoutRetryPolicy();  // 30000 milliseconds timeout

        // The operation should be retried, as the elapsed time is less than the timeout.
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldNotRetryAfterTimeout(): void
    {
        $exception = new Exception("Exception");
        $context = $this->createMock(RetryContext::class);

        $context->method('getStartTime')
            ->willReturn(microtime(true) - 30.5);  // 30500 milliseconds ago

        $retryPolicy = new TimeoutRetryPolicy();  // 30000 milliseconds timeout

        // The operation should not be retried, as the elapsed time is greater than the timeout.
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryJustBeforeTimeout(): void
    {
        $exception = new Exception("Exception");
        $context = $this->createMock(RetryContext::class);

        // 999 milliseconds ago
        $context->method('getStartTime')
            ->willReturn(microtime(true) - 29.999); // 29999.9 milliseconds ago

        $retryPolicy = new TimeoutRetryPolicy();  // 30000 milliseconds timeout

        // The operation should be retried, as the elapsed time is just before the timeout.
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldNotRetryJustAfterTimeout(): void
    {
        $exception = new Exception("Exception");
        $context = $this->createMock(RetryContext::class);

        // 1001 milliseconds ago
        $context->method('getStartTime')
            ->willReturn(microtime(true) - 30.001);

        $retryPolicy = new TimeoutRetryPolicy();  // 30000 milliseconds timeout

        // The operation should not be retried, as the elapsed time is just after the timeout.
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryExactlyAtTimeout(): void
    {
        $exception = new Exception("Exception");
        $context = $this->createMock(RetryContext::class);

        // exactly 1000 milliseconds ago
        $context->method('getStartTime')
            ->willReturn(microtime(true) - 30.0);

        $retryPolicy = new TimeoutRetryPolicy();  // 30000 milliseconds timeout

        // The operation should not be retried, as the elapsed time is exactly at the timeout.
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }
}
