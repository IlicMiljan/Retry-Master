<?php

namespace IlicMiljan\RetryMaster\Statistics;

interface RetryStatistics
{
    public function incrementTotalAttempts(): void;

    public function incrementSuccessfulAttempts(): void;

    public function incrementFailedAttempts(): void;

    public function incrementSleepTime(int $milliseconds): void;

    public function getTotalAttempts(): int;

    public function getSuccessfulAttempts(): int;

    public function getFailedAttempts(): int;

    public function getTotalSleepTimeMilliseconds(): int;
}
