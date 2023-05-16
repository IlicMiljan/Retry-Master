<?php

namespace IlicMiljan\RetryMaster\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use IlicMiljan\RetryMaster\RetryTemplate;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Callback\RecoveryCallback;
use IlicMiljan\RetryMaster\Context\RetryContext;

class RetryTemplateTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRetryTemplateRetriesOperation(): void
    {
        $retryTemplate = new RetryTemplate();

        $retryCount = 0;
        $retryCallback = new class ($retryCount) implements RetryCallback {
            private int $retryCount;

            public function __construct(int &$retryCount)
            {
                $this->retryCount = &$retryCount;
            }
            public function doWithRetry(RetryContext $context): int
            {
                $this->retryCount++;
                if ($this->retryCount < 3) {
                    throw new Exception("Test exception");
                }
                return $this->retryCount;
            }
        };

        $result = $retryTemplate->execute($retryCallback);

        $this->assertEquals(3, $result);
    }

    /**
     * @throws Exception
     */
    public function testRetryTemplateCallsRecoveryCallback(): void
    {
        $retryTemplate = new RetryTemplate();

        $retryCallback = new class implements RetryCallback {
            public function doWithRetry(RetryContext $context)
            {
                throw new Exception("Test exception");
            }
        };

        $recoveryCallback = new class implements RecoveryCallback {
            public function recover(RetryContext $context): string
            {
                return "recovery";
            }
        };

        $result = $retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);

        $this->assertEquals("recovery", $result);
    }
}
