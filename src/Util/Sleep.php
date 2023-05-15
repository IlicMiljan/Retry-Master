<?php

namespace IlicMiljan\RetryMaster\Util;

class Sleep
{
    public static function milliseconds(int $milliseconds): void
    {
        $seconds = floor($milliseconds / 1000);
        $nanoseconds = ($milliseconds % 1000) * 1000000;

        time_nanosleep(intval($seconds), $nanoseconds);
    }
}
