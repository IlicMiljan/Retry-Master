<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

class AlwaysRetryPolicy implements RetryPolicy
{
    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        return true;
    }
}
