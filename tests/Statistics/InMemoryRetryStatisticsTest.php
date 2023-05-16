<?php

namespace IlicMiljan\RetryMaster\Tests\Statistics;

use IlicMiljan\RetryMaster\Statistics\InMemoryRetryStatistics;
use PHPUnit\Framework\TestCase;

class InMemoryRetryStatisticsTest extends TestCase
{
    public function testInitialValues(): void
    {
        $statistics = new InMemoryRetryStatistics();

        $this->assertSame(0, $statistics->getTotalAttempts());
        $this->assertSame(0, $statistics->getSuccessfulAttempts());
        $this->assertSame(0, $statistics->getFailedAttempts());
        $this->assertSame(0, $statistics->getTotalSleepTimeMilliseconds());
    }

    public function testIncrementTotalAttempts(): void
    {
        $statistics = new InMemoryRetryStatistics();

        $statistics->incrementTotalAttempts();

        $this->assertSame(1, $statistics->getTotalAttempts());
    }

    public function testIncrementSuccessfulAttempts(): void
    {
        $statistics = new InMemoryRetryStatistics();

        $statistics->incrementSuccessfulAttempts();

        $this->assertSame(1, $statistics->getSuccessfulAttempts());
    }

    public function testIncrementFailedAttempts(): void
    {
        $statistics = new InMemoryRetryStatistics();

        $statistics->incrementFailedAttempts();

        $this->assertSame(1, $statistics->getFailedAttempts());
    }

    public function testIncrementSleepTime(): void
    {
        $statistics = new InMemoryRetryStatistics();

        $statistics->incrementSleepTime(500);

        $this->assertSame(500, $statistics->getTotalSleepTimeMilliseconds());

        $statistics->incrementSleepTime(300);

        $this->assertSame(800, $statistics->getTotalSleepTimeMilliseconds());
    }
}
