<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\CompositeRetryPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use PHPUnit\Framework\TestCase;

class CompositeRetryPolicyTest extends TestCase
{
    public function testShouldRetryOptimistic(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);

        $alwaysRetryPolicy = $this->createMock(RetryPolicy::class);
        $alwaysRetryPolicy->method('shouldRetry')->willReturn(true);

        $neverRetryPolicy = $this->createMock(RetryPolicy::class);
        $neverRetryPolicy->method('shouldRetry')->willReturn(false);

        $retryPolicy = new CompositeRetryPolicy([$alwaysRetryPolicy, $neverRetryPolicy]);

        // In optimistic mode, the operation is retried if any of the policies allows it
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryMixedOptimistic(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);

        $alwaysRetryPolicy = $this->createMock(RetryPolicy::class);
        $alwaysRetryPolicy->method('shouldRetry')->willReturn(true);

        $sometimesRetryPolicy = $this->createMock(RetryPolicy::class);
        $sometimesRetryPolicy->method('shouldRetry')->willReturnOnConsecutiveCalls(true, false);

        $retryPolicy = new CompositeRetryPolicy([$alwaysRetryPolicy, $sometimesRetryPolicy]);

        // In optimistic mode, the operation is retried if any of the policies allows it
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryPessimistic(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);

        $alwaysRetryPolicy = $this->createMock(RetryPolicy::class);
        $alwaysRetryPolicy->method('shouldRetry')->willReturn(true);

        $neverRetryPolicy = $this->createMock(RetryPolicy::class);
        $neverRetryPolicy->method('shouldRetry')->willReturn(false);

        $retryPolicy = new CompositeRetryPolicy([$alwaysRetryPolicy, $neverRetryPolicy], false);

        // In pessimistic mode, the operation is retried only if all policies allow it
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryAllTruePessimistic(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);

        $alwaysRetryPolicy = $this->createMock(RetryPolicy::class);
        $alwaysRetryPolicy->method('shouldRetry')->willReturn(true);

        $retryPolicy = new CompositeRetryPolicy([$alwaysRetryPolicy, $alwaysRetryPolicy], false);

        // In pessimistic mode, the operation is retried only if all policies allow it
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));
    }

    public function testShouldRetryAllFalsePessimistic(): void
    {
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);

        $neverRetryPolicy = $this->createMock(RetryPolicy::class);
        $neverRetryPolicy->method('shouldRetry')->willReturn(false);

        $retryPolicy = new CompositeRetryPolicy([$neverRetryPolicy, $neverRetryPolicy], false);

        // In pessimistic mode, the operation is retried only if all policies allow it
        $this->assertFalse($retryPolicy->shouldRetry($exception, $context));
    }
}
