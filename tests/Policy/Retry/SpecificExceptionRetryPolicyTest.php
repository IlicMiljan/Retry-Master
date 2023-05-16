<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\SpecificExceptionRetryPolicy;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SpecificExceptionRetryPolicyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $exception1 = new RuntimeException("Exception 1");
        $exception2 = new Exception("Exception 2");

        $context = $this->createMock(RetryContext::class);

        $retryPolicy = new SpecificExceptionRetryPolicy(RuntimeException::class);

        // Retry is allowed when the exception is an instance of the configured class
        $this->assertTrue($retryPolicy->shouldRetry($exception1, $context));

        // Retry is not allowed when the exception is not an instance of the configured class
        $this->assertFalse($retryPolicy->shouldRetry($exception2, $context));
    }
}
