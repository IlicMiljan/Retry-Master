<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Backoff;

use IlicMiljan\RetryMaster\Policy\Backoff\NoBackoffPolicy;
use PHPUnit\Framework\TestCase;

class NoBackoffPolicyTest extends TestCase
{
    public function testBackoff(): void
    {
        $backoffPolicy = new NoBackoffPolicy();

        for ($i = 1; $i <= 10; $i++) {
            $backoff = $backoffPolicy->backoff($i);

            // Check that backoff is always zero.
            $this->assertEquals(0, $backoff);
        }
    }
}
