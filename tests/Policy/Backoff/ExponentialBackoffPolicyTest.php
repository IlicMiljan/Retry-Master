<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Backoff;

use IlicMiljan\RetryMaster\Policy\Backoff\ExponentialBackoffPolicy;
use PHPUnit\Framework\TestCase;

class ExponentialBackoffPolicyTest extends TestCase
{
    public function testBackoff(): void
    {
        $backoffPolicy = new ExponentialBackoffPolicy();

        // Test the first few backoff periods.
        $this->assertEquals(1000, $backoffPolicy->backoff(1));  // 1000 * 2^(1-1) = 1000
        $this->assertEquals(2000, $backoffPolicy->backoff(2));  // 1000 * 2^(2-1) = 2000
        $this->assertEquals(4000, $backoffPolicy->backoff(3));  // 1000 * 2^(3-1) = 4000
        $this->assertEquals(8000, $backoffPolicy->backoff(4));  // 1000 * 2^(4-1) = 8000
    }

    public function testBackoffWithDifferentInitialAndMultiplier(): void
    {
        $backoffPolicy = new ExponentialBackoffPolicy(500, 3);

        // Test the first few backoff periods.
        $this->assertEquals(500, $backoffPolicy->backoff(1));   // 500 * 3^(1-1) = 500
        $this->assertEquals(1500, $backoffPolicy->backoff(2));  // 500 * 3^(2-1) = 1500
        $this->assertEquals(4500, $backoffPolicy->backoff(3));  // 500 * 3^(3-1) = 4500
        $this->assertEquals(13500, $backoffPolicy->backoff(4)); // 500 * 3^(4-1) = 13500
    }
}
