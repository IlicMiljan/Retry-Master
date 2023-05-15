<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

interface RetryPolicy
{
    public function shouldRetry(Exception $e, RetryContext $context): bool;
}
