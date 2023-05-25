<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\NonRepeatingExceptionRetryPolicy;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NonRepeatingExceptionRetryPolicyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $exception1 = new Exception("Exception 1");
        $exception2 = new RuntimeException("Exception 2");
        $exception3 = new Exception("Exception 3");

        $context = $this->createMock(RetryContext::class);
        $context->method('getExceptions')->willReturnOnConsecutiveCalls(
            // Assert when getExceptionCount() == 1, getExceptions() is not called
            [$exception1, $exception1],
            [$exception1, $exception1, $exception2],
            [$exception1, $exception2, $exception3, $exception3]
        );
        $context->method('getExceptionCount')->willReturnOnConsecutiveCalls(1, 2, 3, 4);

        $retryPolicy = new NonRepeatingExceptionRetryPolicy();

        // Retry is allowed when there was no previous exception
        $this->assertTrue($retryPolicy->shouldRetry($exception1, $context));

        // Retry is not allowed when the previous exception is of the same type
        $this->assertFalse($retryPolicy->shouldRetry($exception1, $context));

        // Retry is allowed when the previous exception is of a different type
        $this->assertTrue($retryPolicy->shouldRetry($exception1, $context));

        // Retry is not allowed when the previous exception is of the same type
        $this->assertFalse($retryPolicy->shouldRetry($exception1, $context));
    }
}
