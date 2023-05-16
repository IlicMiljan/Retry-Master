<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Backoff;

use IlicMiljan\RetryMaster\Policy\Backoff\UniformRandomBackoffPolicy;
use PHPUnit\Framework\TestCase;

class UniformRandomBackoffPolicyTest extends TestCase
{
    public function testUniformRandomBackoff(): void
    {
        $minInterval = 1000;
        $maxInterval = 2000;
        $backoffPolicy = new UniformRandomBackoffPolicy($minInterval, $maxInterval);

        for ($i = 1; $i <= 10; $i++) {
            $backoff = $backoffPolicy->backoff($i);

            // Check that backoff is within the defined interval.
            $this->assertGreaterThanOrEqual($minInterval, $backoff);
            $this->assertLessThanOrEqual($maxInterval, $backoff);
        }
    }
}
