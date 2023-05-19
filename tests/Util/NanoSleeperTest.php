<?php

namespace IlicMiljan\RetryMaster\Tests\Util;

use IlicMiljan\RetryMaster\Util\NanoSleeper;
use PHPUnit\Framework\TestCase;

class NanoSleeperTest extends TestCase
{
    public function testMillisecondsFunction(): void
    {
        $nanoSleeper = new NanoSleeper();

        $startTime = microtime(true) * 1000;

        $nanoSleeper->milliseconds(10);

        $endTime = microtime(true) * 1000;

        $this->assertGreaterThanOrEqual(10, $endTime - $startTime);
    }

    public function testConvertMillisecondsToSecondsAndNanoseconds(): void
    {
        $nanoSleeper = new NanoSleeper();

        // Test conversion for 1050 milliseconds.
        [$seconds, $nanoseconds] = $nanoSleeper->convertMillisecondsToSecondsAndNanoseconds(1050);
        $this->assertSame(1, $seconds);
        $this->assertSame(50_000_000, $nanoseconds);

        // Test conversion for 2000 milliseconds.
        [$seconds, $nanoseconds] = $nanoSleeper->convertMillisecondsToSecondsAndNanoseconds(2000);
        $this->assertSame(2, $seconds);
        $this->assertSame(0, $nanoseconds);

        // Test conversion for 999 milliseconds.
        [$seconds, $nanoseconds] = $nanoSleeper->convertMillisecondsToSecondsAndNanoseconds(999);
        $this->assertSame(0, $seconds);
        $this->assertSame(999_000_000, $nanoseconds);

        // Test conversion for 0 milliseconds.
        [$seconds, $nanoseconds] = $nanoSleeper->convertMillisecondsToSecondsAndNanoseconds(0);
        $this->assertSame(0, $seconds);
        $this->assertSame(0, $nanoseconds);
    }
}
