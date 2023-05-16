<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Backoff;

use IlicMiljan\RetryMaster\Policy\Backoff\ExponentialRandomBackoffPolicy;
use PHPUnit\Framework\TestCase;

class ExponentialRandomBackoffPolicyTest extends TestCase
{
    public function testBackoffWithinRange(): void
    {
        $backoffPolicy = new ExponentialRandomBackoffPolicy(1000, 2, 8000);

        for ($i = 1; $i <= 10; $i++) {
            $backoff = $backoffPolicy->backoff($i);

            // Calculate expected interval.
            $expectedInterval = min(
                intval(1000 * pow(2, $i - 1)),
                8000
            );

            // Check that backoff is within expected range.
            $this->assertGreaterThanOrEqual(1000, $backoff);
            $this->assertLessThanOrEqual($expectedInterval, $backoff);
        }
    }

    public function testBackoffDoesNotExceedMaxInterval(): void
    {
        $backoffPolicy = new ExponentialRandomBackoffPolicy(1000, 2, 5000);

        for ($i = 1; $i <= 10; $i++) {
            $backoff = $backoffPolicy->backoff($i);

            // Check that backoff does not exceed maximum interval.
            $this->assertLessThanOrEqual(5000, $backoff);
        }
    }
}
