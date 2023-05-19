<?php

namespace IlicMiljan\RetryMaster\Tests;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;
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
     * @var BackoffPolicy&MockObject
     */
    private $backoffPolicy;
    /**
     * @var RetryStatistics&MockObject
     */
    private $retryStatistics;
    /**
     * @var Sleeper&MockObject
     */
    private $sleeper;
    /**
     * @var LoggerInterface&MockObject
     */
    private $logger;

    private RetryTemplate $retryTemplate;

    protected function setUp(): void
    {
        $this->retryPolicy = $this->createMock(RetryPolicy::class);
        $this->backoffPolicy = $this->createMock(BackoffPolicy::class);
        $this->retryStatistics = $this->createMock(RetryStatistics::class);
        $this->sleeper = $this->createMock(Sleeper::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->retryTemplate = new RetryTemplate(
            $this->retryPolicy,
            $this->backoffPolicy,
            $this->retryStatistics,
            $this->sleeper,
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
        $retryCallback->expects($this->once())->method('doWithRetry')->willReturn('Success');

        $this->logger->expects($this->once())
            ->method('info')
            ->with(
                $this->equalTo('Operation succeeded on attempt'),
                $this->callback(function ($context) {
                    return isset($context['attempt']) && isset($context['successfulAttempts']) && isset($context['totalAttempts']);
                })
            );

        $result = $this->retryTemplate->execute($retryCallback);
        $this->assertEquals('Success', $result);
    }

    /**
     * @throws Exception
     */
    public function testExecuteSuccessAfterFailure(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->exactly(2))
            ->method('doWithRetry')
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new Exception('error')),
                $this->returnValue('success')
            );

        $this->retryPolicy->expects($this->exactly(1))
            ->method('shouldRetry')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->backoffPolicy->expects($this->once())
            ->method('backoff')
            ->with($this->equalTo(1))
            ->willReturn(100);

        $this->sleeper->expects($this->once())
            ->method('milliseconds')
            ->with($this->equalTo(100));

        $result = $this->retryTemplate->execute($retryCallback);
        $this->assertEquals('success', $result);
    }


    public function testExecuteFailure(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('Error'));
        $this->retryPolicy->expects($this->once())->method('shouldRetry')->willReturn(false);

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('Operation failed'),
                $this->callback(function ($context) {
                    return isset($context['exception']) && isset($context['failedAttempts']) && isset($context['totalAttempts']);
                })
            );

        $this->expectException(Exception::class);
        $this->retryTemplate->execute($retryCallback);
    }

    /**
     * @throws Exception
     */
    public function testExecuteFailureUpdatesRetryContext(): void
    {
        $exception = new Exception('Error');

        // RetryCallback will throw our custom Exception
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->exactly(2))
            ->method('doWithRetry')
            ->willThrowException($exception);

        $this->retryPolicy->expects($this->exactly(2))
            ->method('shouldRetry')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                [
                    $this->equalTo('Sleeping before next attempt'),
                    $this->callback(function ($context) {
                        return isset($context['sleepTime']) && isset($context['totalSleepTime']);
                    })
                ],
                [
                    $this->equalTo('Recovery operation succeeded'),
                    $this->callback(function ($context) {
                        return true;
                    })
                ]
            );

        $recoveryCallback = $this->createMock(RecoveryCallback::class);
        $recoveryCallback->expects($this->once())
            ->method('recover')
            ->will($this->returnCallback(function (RetryContext $context) use ($exception) {
                // Assert that the RetryContext has the correct exception
                $this->assertEquals($exception, $context->getLastException());
                return 'Recovered';
            }));

        $result = $this->retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
        $this->assertEquals('Recovered', $result);
    }


    /**
     * @throws Exception
     */
    public function testExecuteWithRecovery(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $recoveryCallback = $this->createMock(RecoveryCallback::class);

        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('error'));
        $recoveryCallback->expects($this->once())->method('recover')->willReturn('Recovery Success');
        $this->retryPolicy->expects($this->once())->method('shouldRetry')->willReturn(false);

        $result = $this->retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
        $this->assertEquals('Recovery Success', $result);
    }

    public function testExecuteWithFailedRecovery(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $recoveryCallback = $this->createMock(RecoveryCallback::class);

        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('error'));
        $recoveryCallback->expects($this->once())->method('recover')->willThrowException(new Exception('recovery error'));
        $this->retryPolicy->expects($this->once())->method('shouldRetry')->willReturn(false);

        $this->logger->expects($this->exactly(2))
            ->method('error')
            ->withConsecutive(
                [
                    $this->equalTo('Operation failed'),
                    $this->callback(function ($context) {
                        return isset($context['exception']);
                    })
                ],
                [
                    $this->equalTo('Recovery failed'),
                    $this->callback(function ($context) {
                        return isset($context['exception']);
                    })
                ]
            );

        $this->expectException(Exception::class);
        $this->retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
    }

    /**
     * @throws Exception
     */
    public function testExecuteLogsAndUpdatesStatisticsOnSuccess(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->once())->method('doWithRetry')->willReturn('Success');

        $this->retryStatistics->expects($this->once())->method('incrementTotalAttempts');
        $this->retryStatistics->expects($this->once())->method('incrementSuccessfulAttempts');
        $this->logger->expects($this->once())->method('info');

        $result = $this->retryTemplate->execute($retryCallback);
        $this->assertEquals('Success', $result);
    }

    public function testExecuteLogsAndUpdatesStatisticsOnFailure(): void
    {
        $retryCallback = $this->createMock(RetryCallback::class);
        $retryCallback->expects($this->once())->method('doWithRetry')->willThrowException(new Exception('Error'));

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

        $retryCallback->expects($this->exactly(2))
            ->method('doWithRetry')
            ->willThrowException(new Exception('Error'));

        $recoveryCallback->expects($this->once())
            ->method('recover')
            ->willReturn('Recovery Success');

        $this->retryPolicy->expects($this->exactly(2))
            ->method('shouldRetry')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->retryStatistics->expects($this->exactly(2))
            ->method('incrementTotalAttempts');
        $this->retryStatistics->expects($this->exactly(2))
            ->method('incrementFailedAttempts');
        $this->retryStatistics->expects($this->once())
            ->method('incrementSleepTime');
        $this->logger->expects($this->exactly(2))
            ->method('error');
        $this->logger->expects($this->exactly(2))
            ->method('info');

        $result = $this->retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
        $this->assertEquals('Recovery Success', $result);
    }

    public function testGetRetryStatistics(): void
    {
        $this->assertSame($this->retryStatistics, $this->retryTemplate->getRetryStatistics());
    }
}
