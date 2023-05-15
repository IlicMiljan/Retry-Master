<?php

namespace IlicMiljan\RetryMaster\Callback;

use IlicMiljan\RetryMaster\Context\RetryContext;

interface RecoveryCallback
{
    /**
     * @param RetryContext $context
     * @return mixed
     */
    public function recover(RetryContext $context);
}
