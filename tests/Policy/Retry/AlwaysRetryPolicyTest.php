<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use IlicMiljan\RetryMaster\Policy\Retry\AlwaysRetryPolicy;
use PHPUnit\Framework\TestCase;

class AlwaysRetryPolicyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $retryPolicy = new AlwaysRetryPolicy();
        $exception = new Exception();
        $context = $this->createMock(RetryContext::class);

        // AlwaysRetryPolicy should always return true, irrespective of the exception or the context.
        $this->assertTrue($retryPolicy->shouldRetry($exception, $context));

        $anotherException = new Exception('Another exception');
        $anotherContext = $this->createMock(RetryContext::class);

        $this->assertTrue($retryPolicy->shouldRetry($anotherException, $anotherContext));
    }
}
