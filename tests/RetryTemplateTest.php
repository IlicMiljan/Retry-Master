<?php

namespace IlicMiljan\RetryMaster\Tests;

use Exception;
use IlicMiljan\RetryMaster\Policy\Backoff\BackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;
use PHPUnit\Framework\TestCase;
use IlicMiljan\RetryMaster\RetryTemplate;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Callback\RecoveryCallback;
use IlicMiljan\RetryMaster\Context\RetryContext;
use Psr\Log\LoggerInterface;

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
                    throw new Exception("Test Exception");
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
                throw new Exception("Test Exception");
            }
        };

        $recoveryCallback = new class implements RecoveryCallback {
            public function recover(RetryContext $context): string
            {
                return "Recovery";
            }
        };

        $result = $retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);

        $this->assertEquals("Recovery", $result);
    }

    public function testStatisticsDuringExecute(): void
    {
        $retryPolicy = $this->createMock(RetryPolicy::class);
        $backoffPolicy = $this->createMock(BackoffPolicy::class);
        $retryStatistics = $this->createMock(RetryStatistics::class);
        $retryCallback = $this->createMock(RetryCallback::class);

        $retryPolicy->method('shouldRetry')->willReturn(false);
        $retryCallback->method('doWithRetry')->willThrowException(new Exception());

        $retryStatistics->expects($this->once())->method('incrementTotalAttempts');
        $retryStatistics->expects($this->once())->method('incrementFailedAttempts');

        $retryTemplate = new RetryTemplate(
            $retryPolicy,
            $backoffPolicy,
            $retryStatistics
        );

        try {
            $retryTemplate->execute($retryCallback);
        } catch (Exception $e) {
            // Handle exception
        }
    }

    /**
     * @throws Exception
     */
    public function testStatisticsDuringExecuteWithRecovery(): void
    {
        $retryPolicy = $this->createMock(RetryPolicy::class);
        $backoffPolicy = $this->createMock(BackoffPolicy::class);
        $retryStatistics = $this->createMock(RetryStatistics::class);
        $retryCallback = $this->createMock(RetryCallback::class);
        $recoveryCallback = $this->createMock(RecoveryCallback::class);

        $retryPolicy->method('shouldRetry')->willReturnOnConsecutiveCalls(true, false);
        $retryCallback->method('doWithRetry')->willThrowException(new Exception());
        $recoveryCallback->method('recover')->willReturn(true);

        $retryStatistics->expects($this->exactly(2))->method('incrementTotalAttempts');
        $retryStatistics->expects($this->exactly(2))->method('incrementFailedAttempts');

        $retryTemplate = new RetryTemplate(
            $retryPolicy,
            $backoffPolicy,
            $retryStatistics
        );

        $retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
    }


    /**
     * @throws Exception
     */
    public function testLoggingBehaviorDuringExecuteSuccess(): void
    {
        // Arrange
        $retryPolicy = $this->createMock(RetryPolicy::class);
        $backoffPolicy = $this->createMock(BackoffPolicy::class);
        $logger = $this->createMock(LoggerInterface::class);
        $retryCallback = $this->createMock(RetryCallback::class);

        $retryPolicy->method('shouldRetry')->willReturn(false);
        $retryCallback->method('doWithRetry')->willReturn(null);

        $logger->expects($this->once())
            ->method('info')
            ->with(
                $this->equalTo('Operation succeeded on attempt'),
                $this->callback(function ($context) {
                    // Add checks for 'successfulAttempts', 'totalAttempts' here
                    return $context['attempt'] === 1 &&
                        $context['successfulAttempts'] === 1 &&
                        $context['totalAttempts'] === 1;
                })
            );

        $retryTemplate = new RetryTemplate(
            $retryPolicy,
            $backoffPolicy,
            null,
            $logger
        );

        // Act
        $retryTemplate->execute($retryCallback);
    }


    /**
     * @throws Exception
     */
    public function testLoggingBehaviorDuringExecuteWithRecoverySuccess(): void
    {
        // Arrange
        $retryPolicy = $this->createMock(RetryPolicy::class);
        $backoffPolicy = $this->createMock(BackoffPolicy::class);
        $logger = $this->createMock(LoggerInterface::class);
        $retryCallback = $this->createMock(RetryCallback::class);
        $recoveryCallback = $this->createMock(RecoveryCallback::class);

        $retryPolicy->method('shouldRetry')->will($this->returnCallback(function ($exception, $context) {
            return $context->getRetryCount() < 2;
        }));

        $retryCallback->method('doWithRetry')->will($this->returnCallback(function ($context) {
            if ($context->getRetryCount() < 2) {
                throw new Exception('First attempt will fail');
            }
            return 'Successful Result';  // Second attempt will succeed.
        }));

        $recoveryCallback->method('recover')->willReturn(null);

        $logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                [
                    $this->equalTo('Sleeping before next attempt'),
                    $this->callback(function ($context) {
                        return isset($context['sleepTime']) &&
                            isset($context['totalSleepTime']);
                    })
                ],
                [
                    $this->equalTo('Operation succeeded on attempt'),
                    $this->callback(function ($context) {
                        return $context['attempt'] === 2 &&
                            $context['successfulAttempts'] === 1 &&
                            $context['totalAttempts'] === 2;
                    })
                ]
            );

        $logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('Operation failed'),
                $this->callback(function ($context) {
                    return isset($context['exception']) &&
                        $context['failedAttempts'] === 1 &&
                        $context['totalAttempts'] === 1;
                })
            );

        $retryTemplate = new RetryTemplate(
            $retryPolicy,
            $backoffPolicy,
            null,
            $logger
        );

        // Act
        $retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
    }
}
