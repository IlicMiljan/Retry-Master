<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Backoff;

use IlicMiljan\RetryMaster\Policy\Backoff\ExponentialRandomBackoffPolicy;
use IlicMiljan\RetryMaster\Util\Random;
use PHPUnit\Framework\TestCase;

class ExponentialRandomBackoffPolicyTest extends TestCase
{
    public function testBackoff(): void
    {
        $random = $this->createMock(Random::class);
        $random->method('nextInt')->willReturnCallback(fn($min, $max) => $min);

        $backoffPolicy = new ExponentialRandomBackoffPolicy();
        $backoffPolicy->setRandom($random);

        $this->assertEquals(1000, $backoffPolicy->backoff(1));
        $this->assertEquals(2000, $backoffPolicy->backoff(2));
        $this->assertEquals(4000, $backoffPolicy->backoff(3));
        $this->assertEquals(8000, $backoffPolicy->backoff(4));
        $this->assertEquals(16000, $backoffPolicy->backoff(5));
        $this->assertEquals(30000, $backoffPolicy->backoff(6));
    }

    public function testBackoffWithDifferentInitialParameters(): void
    {
        $random = $this->createMock(Random::class);
        $random->method('nextInt')->willReturnCallback(fn($min, $max) => $max);

        $backoffPolicy = new ExponentialRandomBackoffPolicy(500, 3, 15000);
        $backoffPolicy->setRandom($random);

        $this->assertEquals(1500, $backoffPolicy->backoff(1));
        $this->assertEquals(4500, $backoffPolicy->backoff(2));
        $this->assertEquals(13500, $backoffPolicy->backoff(3));
        $this->assertEquals(15000, $backoffPolicy->backoff(4));
    }
}
