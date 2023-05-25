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
        $this->assertEmpty($context->getExceptions());
        $this->assertEquals(0, $context->getExceptionCount());

        // Test incrementing retry count.
        $context->incrementRetryCount();
        $this->assertEquals(1, $context->getRetryCount());
        $this->assertNotNull($context->getStartTime());

        // Test setting and getting last exception.
        $exception = new Exception('Test exception');
        $context->addException($exception);
        $this->assertSame([$exception], $context->getExceptions());
        $this->assertEquals(1, $context->getExceptionCount());

        // Test getting start time.
        $this->assertIsFloat($context->getStartTime());
    }
}
