<?php

namespace IlicMiljan\RetryMaster\Statistics;

class InMemoryRetryStatistics implements RetryStatistics
{
    private int $totalAttempts = 0;
    private int $successfulAttempts = 0;
    private int $failedAttempts = 0;
    private int $totalSleepTimeMilliseconds = 0;

    public function incrementTotalAttempts(): void
    {
        $this->totalAttempts++;
    }

    public function incrementSuccessfulAttempts(): void
    {
        $this->successfulAttempts++;
    }

    public function incrementFailedAttempts(): void
    {
        $this->failedAttempts++;
    }

    public function incrementSleepTime(int $milliseconds): void
    {
        $this->totalSleepTimeMilliseconds += $milliseconds;
    }

    public function getTotalAttempts(): int
    {
        return $this->totalAttempts;
    }

    public function getSuccessfulAttempts(): int
    {
        return $this->successfulAttempts;
    }

    public function getFailedAttempts(): int
    {
        return $this->failedAttempts;
    }

    public function getTotalSleepTimeMilliseconds(): int
    {
        return $this->totalSleepTimeMilliseconds;
    }
}
