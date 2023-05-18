<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Backoff;

use IlicMiljan\RetryMaster\Policy\Backoff\FixedBackoffPolicy;
use PHPUnit\Framework\TestCase;

class FixedBackoffPolicyTest extends TestCase
{
    public function testBackoff(): void
    {
        $backoffPolicy = new FixedBackoffPolicy();

        for ($i = 1; $i <= 10; $i++) {
            $backoff = $backoffPolicy->backoff($i);

            // Check that backoff is always the same.
            $this->assertEquals(1000, $backoff);
        }
    }

    public function testBackoffWithDifferentInitialParameters(): void
    {
        $backoffPolicy = new FixedBackoffPolicy(2000);

        for ($i = 1; $i <= 10; $i++) {
            $backoff = $backoffPolicy->backoff($i);

            // Check that backoff is always the same.
            $this->assertEquals(2000, $backoff);
        }
    }
}
