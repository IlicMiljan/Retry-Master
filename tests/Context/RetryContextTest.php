<?php

namespace IlicMiljan\RetryMaster\Tests\Context;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
use PHPUnit\Framework\TestCase;

class RetryContextTest extends TestCase
{
    public function testRetryContext(): void
    {
        $context = new RetryContext();

        // Test initial state.
        $this->assertEquals(0, $context->getRetryCount());
        $this->assertNull($context->getLastException());

        // Test incrementing retry count.
        $context->incrementRetryCount();
        $this->assertEquals(1, $context->getRetryCount());
        $this->assertNotNull($context->getStartTime());

        // Test setting and getting last exception.
        $exception = new Exception('Test exception');
        $context->setLastException($exception);
        $this->assertSame($exception, $context->getLastException());

        // Test getting start time.
        $this->assertIsFloat($context->getStartTime());
    }
}
