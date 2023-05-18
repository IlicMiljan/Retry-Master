<?php

namespace IlicMiljan\RetryMaster\Tests\Policy\Backoff;

use IlicMiljan\RetryMaster\Policy\Backoff\UniformRandomBackoffPolicy;
use IlicMiljan\RetryMaster\Util\Random;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;
use PHPUnit\Framework\TestCase;

class UniformRandomBackoffPolicyTest extends TestCase
{
    public function testBackoff(): void
    {
        $random = $this->createMock(Random::class);

        $random->method('nextInt')->willReturnOnConsecutiveCalls(
            new ReturnCallback(fn($min, $max) => $min),
            new ReturnCallback(fn($min, $max) => $max)
        );

        $backoffPolicy = new UniformRandomBackoffPolicy();
        $backoffPolicy->setRandom($random);

        $this->assertEquals(1000, $backoffPolicy->backoff(1));
        $this->assertEquals(2000, $backoffPolicy->backoff(2));
    }

    public function testBackoffWithDifferentInitialParameters(): void
    {
        $random = $this->createMock(Random::class);

        $random->method('nextInt')->willReturnOnConsecutiveCalls(
            new ReturnCallback(fn($min, $max) => $min),
            new ReturnCallback(fn($min, $max) => $max)
        );

        $backoffPolicy = new UniformRandomBackoffPolicy(500, 1500);
        $backoffPolicy->setRandom($random);

        $this->assertEquals(500, $backoffPolicy->backoff(1));
        $this->assertEquals(1500, $backoffPolicy->backoff(2));
    }
}
