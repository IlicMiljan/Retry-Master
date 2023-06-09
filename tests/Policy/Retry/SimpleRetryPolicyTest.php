<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\SimpleRetryPolicy;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SimpleRetryPolicyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $exception1 = new Exception("Exception 1");
        $exception2 = new RuntimeException("Exception 2");

        $context = $this->createMock(RetryContext::class);
        $context->method('getRetryCount')->willReturnOnConsecutiveCalls(1, 2, 3, 4, 5, 6);

        $retryPolicy = new SimpleRetryPolicy(3, [Exception::class]);

        // Retry is allowed when the number of attempts is less than maxAttempts and the exception is in the list of retryable exceptions
        $this->assertTrue($retryPolicy->shouldRetry($exception1, $context));
        $this->assertTrue($retryPolicy->shouldRetry($exception1, $context));
        $this->assertTrue($retryPolicy->shouldRetry($exception1, $context));

        // Retry is not allowed when the number of attempts reaches maxAttempts
        $this->assertFalse($retryPolicy->shouldRetry($exception1, $context));

        // Retry is not allowed when the number of attempts exceeds maxAttempts
        $this->assertFalse($retryPolicy->shouldRetry($exception1, $context));

        // Retry is not allowed when the exception is not in the list of retryable exceptions, even if the number of attempts is less than maxAttempts
        $retryPolicy = new SimpleRetryPolicy(3, []);
        $this->assertFalse($retryPolicy->shouldRetry($exception2, $context));
    }

    public function testShouldRetryWithDefaultMaxAttempts(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);
        $context->method('getRetryCount')->willReturnOnConsecutiveCalls(1, 2, 3, 4);

        $retryPolicy = new SimpleRetryPolicy();  // Use the default maxAttempts value

        // Retry is allowed when the number of attempts is less than or equal to maxAttempts
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));

        // Retry is not allowed when the number of attempts exceeds maxAttempts
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryWhenRetryableExceptionListEmpty(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);
        $context->method('getRetryCount')->willReturn(0);

        $retryPolicy = new SimpleRetryPolicy(3, []);

        // Retry is not allowed when the exception is not in the list of retryable exceptions
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryWithUnrelatedException(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);
        $context->method('getRetryCount')->willReturn(0);

        $retryPolicy = new SimpleRetryPolicy(3, [RuntimeException::class]);

        // Retry is not allowed when the exception is not in the list of retryable exceptions
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }
}
