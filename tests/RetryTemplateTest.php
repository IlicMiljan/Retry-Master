<?php

namespace IlicMiljan\RetryMaster\Tests;

use Exception;
use IlicMiljan\RetryMaster\Logger\NullLogger;
use IlicMiljan\RetryMaster\Policy\Backoff\BackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Backoff\FixedBackoffPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\MaxAttemptsRetryPolicy;
use IlicMiljan\RetryMaster\Policy\Retry\RetryPolicy;
use IlicMiljan\RetryMaster\Statistics\InMemoryRetryStatistics;
use IlicMiljan\RetryMaster\Statistics\RetryStatistics;
use IlicMiljan\RetryMaster\Util\NanoSleeper;
use IlicMiljan\RetryMaster\Util\Sleeper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use IlicMiljan\RetryMaster\RetryTemplate;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Callback\RecoveryCallback;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class RetryTemplateTest extends TestCase
{
    /**
     * @var RetryPolicy&MockObject
     */
    private $retryPolicy;
    /**
     * @var RetryStatistics&MockObject
     */
    private $retryStatistics;
    /**
     * @var LoggerInterface&MockObject
     */
    private $logger;

    private RetryTemplate $retryTemplate;

    protected function setUp(): void
    {
        $this->retryPolicy = $this->createMock(RetryPolicy::class);
        $backoffPolicy = $this->createMock(BackoffPolicy::class);
        $this->retryStatistics = $this->createMock(RetryStatistics::class);
        $sleeper = $this->createMock(Sleeper::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->retryTemplate = new RetryTemplate(
            $this->retryPolicy,
            $backoffPolicy,
            $this->retryStatistics,
            $sleeper,
            $this->logger
        );
    }

    public function testConstructor(): void
    {
        $retryTemplate = new RetryTemplate();

        $retryTemplateReflection = new ReflectionClass($retryTemplate);

        $retryPolicyProperty = $retryTemplateReflection->getProperty('retryPolicy');
        $retryPolicyProperty->setAccessible(true);

        $backoffPolicyProperty = $retryTemplateReflection->getProperty('backoffPolicy');
        $backoffPolicyProperty->setAccessible(true);

        $retryStatisticsProperty = $retryTemplateReflection->getProperty('retryStatistics');
        $retryStatisticsProperty->setAccessible(true);

        $sleeperProperty = $retryTemplateReflection->getProperty('sleeper');
        $sleeperProperty->setAccessible(true);

        $loggerProperty = $retryTemplateReflection->getProperty('logger');
        $loggerProperty->setAccessible(true);

        $this->assertInstanceOf(MaxAttemptsRetryPolicy::class, $retryPolicyProperty->getValue($retryTemplate));
        $this->assertInstanceOf(FixedBackoffPolicy::class, $backoffPolicyProperty->getValue($retryTemplate));
        $this->assertInstanceOf(InMemoryRetryStatistics::class, $retryStatisticsProperty->getValue($retryTemplate));
        $this->assertInstanceOf(NanoSleeper::class, $sleeperProperty->getValue($retryTemplate));
        $this->assertInstanceOf(NullLogger::class, $loggerProperty->getValue($retryTemplate));
    }

    /**
     * @throws Exception
     */
    public function testExecuteSuccess(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->once())->method('doWithRetry')->willReturn('success');

        $result = $this->retryTemplate->execute($retryCallback);
        $this->assertEquals('success', $result);
    }

    public function testExecuteFailure(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('error'));
        $this->retryPolicy->expects($this->once())->method('shouldRetry')->willReturn(false);

        $this->expectException(Exception::class);
        $this->retryTemplate->execute($retryCallback);
    }

    /**
     * @throws Exception
     */
    public function testExecuteWithRecovery(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $recoveryCallback = $this->createMock(RecoveryCallback::class);

        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('error'));
        $recoveryCallback->expects($this->once())->method('recover')->willReturn('recovery success');
        $this->retryPolicy->expects($this->once())->method('shouldRetry')->willReturn(false);

        $result = $this->retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
        $this->assertEquals('recovery success', $result);
    }

    public function testExecuteWithFailedRecovery(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $recoveryCallback = $this->createMock(RecoveryCallback::class);

        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('error'));
        $recoveryCallback->expects($this->once())->method('recover')->willThrowException(new Exception('recovery error'));
        $this->retryPolicy->expects($this->once())->method('shouldRetry')->willReturn(false);

        $this->expectException(Exception::class);
        $this->retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
    }

    /**
     * @throws Exception
     */
    public function testExecuteLogsAndUpdatesStatisticsOnSuccess(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->once())->method('doWithRetry')->willReturn('success');

        $this->retryStatistics->expects($this->once())->method('incrementTotalAttempts');
        $this->retryStatistics->expects($this->once())->method('incrementSuccessfulAttempts');
        $this->logger->expects($this->once())->method('info');

        $result = $this->retryTemplate->execute($retryCallback);
        $this->assertEquals('success', $result);
    }

    public function testExecuteLogsAndUpdatesStatisticsOnFailure(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('error'));

        $this->retryPolicy->expects($this->once())->method('shouldRetry')->willReturn(false);

        $this->retryStatistics->expects($this->once())->method('incrementTotalAttempts');
        $this->retryStatistics->expects($this->once())->method('incrementFailedAttempts');
        $this->logger->expects($this->once())->method('error');

        $this->expectException(Exception::class);
        $this->retryTemplate->execute($retryCallback);
    }

    /**
     * @throws Exception
     */
    public function testExecuteWithRecoveryLogsAndUpdatesStatistics(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $recoveryCallback = $this->createMock(RecoveryCallback::class);

        $retryCallback->expects($this->once())
            ->method('doWithRetry')
            ->willThrowException(new Exception('error'));

        $recoveryCallback->expects($this->once())
            ->method('recover')
            ->willReturn('recovery success');

        $this->retryPolicy->expects($this->once())
            ->method('shouldRetry')
            ->willReturn(false);

        $this->retryStatistics->expects($this->once())
            ->method('incrementTotalAttempts');
        $this->retryStatistics->expects($this->once())
            ->method('incrementFailedAttempts');
        $this->logger->expects($this->once())
            ->method('error');
        $this->logger->expects($this->once())
            ->method('info');

        $result = $this->retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
        $this->assertEquals('recovery success', $result);
    }


    public function testGetRetryStatistics(): void
    {
        $this->assertSame($this->retryStatistics, $this->retryTemplate->getRetryStatistics());
    }
}
