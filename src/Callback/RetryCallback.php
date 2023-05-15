<?php

namespace IlicMiljan\RetryMaster\Callback;

use IlicMiljan\RetryMaster\Context\RetryContext;

interface RetryCallback
{
    /**
     * @param RetryContext $context
     * @return mixed
     */
    public function doWithRetry(RetryContext $context);
}
