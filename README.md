# RetryMaster

RetryMaster is a flexible and extensible PHP library for handling operation retries. It provides a simple, declarative way of managing operations that might fail due to transient issues. By using RetryMaster, you can easily implement robust retry logic with customizable policies for when and how to perform retries.

## Features

- **Flexible Retry Policies**: Choose from a variety of built-in retry policies or create your own. You can easily control how and when retries are performed based on the type and number of exceptions, timeout, maximum attempts and more.
- **Configurable Backoff Policies**: Control the delay between retries using various backoff strategies including fixed delay, exponential backoff or custom backoff logic.
- **Detailed Retry Statistics**: Collect and access detailed statistics about your retry operations, such as total attempts, successful attempts, failed attempts and total sleep time.
- **Easy-to-Use Retry Template**: Use the RetryTemplate to execute operations with retry logic. Simply provide the operation logic and the RetryTemplate handles the rest.
- **Custom Retry and Recovery Callbacks**: Define custom logic to execute on each retry attempt and when all retries fail.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
    - [Customizing Retry Logic](#customizing-retry-logic)
    - [Handling Retry Failures](#handling-retry-failures)
    - [Gathering Retry Statistics](#gathering-retry-statistics)
- [Documentation](#documentation)
    - [Overview](#overview)
    - [Retry Policies](#retry-policies)
    - [Backoff Policies](#backoff-policies)
    - [Retry Statistics](#retry-statistics)
    - [Retry and Recovery Callbacks](#retry-and-recovery-callbacks)
    - [RetryTemplate](#retrytemplate)
    - [Util](#util)
- [License](#license)
- [Credits](#credits)

## Installation

RetryMaster is available as a Composer package. You can add it to your project by running the following command in your terminal:

```bash
composer require ilicmiljan/retry-master
```

This will add RetryMaster to your project's dependencies and download the package to your vendor directory.

After installation, you can use RetryMaster classes by adding the appropriate `use` statements at the top of your PHP files. For example:

```php
use IlicMiljan\RetryMaster\RetryTemplate;
use IlicMiljan\RetryMaster\Policy\Retry\MaxAttemptsRetryPolicy;
use IlicMiljan\RetryMaster\Policy\Backoff\ExponentialBackoffPolicy;
```

Be sure to run `composer dump-autoload` if you're not using a framework that does this automatically.

## Usage

Using RetryMaster in your PHP application involves setting up a `RetryTemplate` and executing your operation using this template. With the introduction of a builder and interfaces, you can use `RetryTemplateBuilder` to conveniently create a `RetryTemplate`. You can customize the retry logic by specifying retry and backoff policies when constructing the RetryTemplate.

Here is a basic example:

```php
use IlicMiljan\RetryMaster\RetryTemplateBuilder;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Context\RetryContext;

$retryTemplate = (new RetryTemplateBuilder())->build();

$retryCallback = new class implements RetryCallback {
    public function doWithRetry(RetryContext $context) {
        // Your operation goes here. For example:
        // return $this->repository->find($id);
    }
};

$result = $retryTemplate->execute($retryCallback);
```

In this example, the operation will be retried up to three times (the default maximum attempts) if an exception is thrown. Between each attempt, there will be a fixed delay of one second (the default backoff policy).

### Customizing Retry Logic

You can specify custom retry and backoff policies when creating the RetryTemplate using the builder:

```php
use IlicMiljan\RetryMaster\RetryTemplateBuilder;
use IlicMiljan\RetryMaster\Policy\Retry\MaxAttemptsRetryPolicy;
use IlicMiljan\RetryMaster\Policy\Backoff\UniformRandomBackoffPolicy;

$retryPolicy = new MaxAttemptsRetryPolicy(5);
$backoffPolicy = new UniformRandomBackoffPolicy(500, 1500);

$retryTemplate = (new RetryTemplateBuilder())
                    ->setRetryPolicy($retryPolicy)
                    ->setBackoffPolicy($backoffPolicy)
                    ->build();
```

In this example, the operation will be retried up to five times, and the delay between attempts will be a random number of milliseconds between 500 and 1500.

### Handling Retry Failures

You can provide a recovery callback to handle cases when all retry attempts fail:

```php
use IlicMiljan\RetryMaster\RetryTemplateBuilder;
use IlicMiljan\RetryMaster\Callback\RetryCallback;
use IlicMiljan\RetryMaster\Callback\RecoveryCallback;
use IlicMiljan\RetryMaster\Context\RetryContext;

$retryTemplate = (new RetryTemplateBuilder())->build();

$retryCallback = new class implements RetryCallback {
    public function doWithRetry(RetryContext $context) {
        // Your operation goes here.
    }
};

$recoveryCallback = new class implements RecoveryCallback {
    public function recover(RetryContext $context) {
        // Your recovery logic goes here. For example:
        // return $this->fallbackRepository->find($id);
    }
};

$result = $retryTemplate->executeWithRecovery($retryCallback, $recoveryCallback);
```

### Gathering Retry Statistics

You can retrieve statistics about retry operations from the RetryTemplate:

```php
$retryStatistics = $retryTemplate->getRetryStatistics();

echo 'Total attempts: ' . $retryStatistics->getTotalAttempts() . "\n";
echo 'Successful attempts: ' . $retryStatistics->getSuccessfulAttempts() . "\n";
echo 'Failed attempts: ' . $retryStatistics->getFailedAttempts() . "\n";
echo 'Total sleep time: ' . $retryStatistics->getTotalSleepTimeMilliseconds() . "ms\n";
```

For more usage examples, please refer to the inline comments in each class.
## Documentation

### Overview

RetryMaster is designed to facilitate the implementation of retry operations in your PHP applications. It provides a set of tools for managing retry logic, including customizable retry and backoff policies and detailed retry statistics.

### Retry Policies

A retry policy determines whether an operation should be retried after a failure. RetryMaster includes several built-in retry policies, such as:

- `AlwaysRetryPolicy`: This policy always allows a retry, irrespective of the type of exception or the number of attempts so far. It can be used in scenarios where you want to keep retrying indefinitely until the operation succeeds. However, it should be used with caution, as it can potentially lead to an infinite loop if the operation always fails.

- `CompositeRetryPolicy`: This policy delegates the decision whether to retry to multiple other policies. It allows combining multiple policies in an optimistic or pessimistic manner. In optimistic mode (the default), the operation is retried if any of the policies allows it. In pessimistic mode, the operation is retried only if all policies allow it.

- `MaxAttemptsRetryPolicy`: This policy allows an operation to be retried a specified maximum number of times. It is useful in scenarios where you want to limit the number of retry attempts for an operation to avoid excessive retries.

- `NeverRetryPolicy`: This policy disallows any retry attempts, regardless of the operation or its result. It is useful in scenarios where you do not want any retries to be performed for a certain operation, irrespective of whether it fails or not.

- `NonRepeatingExceptionRetryPolicy`: This policy allows a retry only if the exception type thrown by the last failed attempt is different from the current exception type. It is beneficial in scenarios where an operation is expected to fail repeatedly with the same type of exception, and retrying would not change the outcome.

- `SimpleRetryPolicy`: This policy retries a failed operation a fixed number of times, and for a specific set of exceptions. It is configurable with a maxAttempts property and a retryableExceptions list. The shouldRetry method will return true if the exception is either in the list of retryable exceptions or if the list is empty, and the maximum number of attempts has not been reached.

- `SpecificExceptionRetryPolicy`: This policy decides to retry a failed operation based on the type of exception that occurred. It is initialized with a specific exception class, and the shouldRetry method will return true if the exception that caused the failure is an instance of the configured class.

- `TimeoutRetryPolicy`: This policy decides to retry a failed operation based on the total elapsed time since the first attempt. It is initialized with a timeout in milliseconds, and the shouldRetry method will return true if the elapsed time since the first attempt is less than the configured timeout.

You can also create your own retry policies by implementing the `RetryPolicy` interface.

### Backoff Policies

A backoff policy determines the delay between retry attempts. RetryMaster includes several built-in backoff policies, such as:

- `ExponentialBackoffPolicy`: This policy provides an exponential backoff, meaning that the wait time between retry attempts increases exponentially with each failed attempt. This is a standard error-handling strategy for network applications and helps to gradually reduce the load on the system during a series of failures.

- `ExponentialRandomBackoffPolicy`: This policy provides an exponential backoff with a random component. The wait time between retry attempts increases exponentially and varies randomly within a range defined by the current backoff interval. This helps to prevent many instances of an application from all retrying at the same time, potentially overwhelming a system or service, a scenario known as the "thundering herd problem".

- `FixedBackoffPolicy`: This policy applies a fixed delay between retry attempts. The wait time is always the same, regardless of the number of attempts. This is useful in situations where the likelihood of a retry succeeding is not related to the number of times it has been tried, and where it's not necessary to increase the delay over time.

- `NoBackoffPolicy`: This policy applies no delay between retry attempts. The retries occur immediately after a failure. This is useful in scenarios where you want to retry an operation immediately after a failure without any delay. However, it should be used cautiously as it can potentially lead to higher load on the system in case of persistent failures, due to the absence of any delay between consecutive retry attempts.

- `UniformRandomBackoffPolicy`: This policy applies a random delay (within a specified range) between retry attempts. The wait time is a random number uniformly distributed between a minimum and maximum interval. This can be used to introduce a random delay between retries to avoid a thundering herd problem.

You can also create your own backoff policies by implementing the `BackoffPolicy` interface.

### Retry Statistics

The `RetryStatistics` interface allows you to gather information about retry operations, such as the total number of attempts, the number of successful attempts, the number of failed attempts, and the total sleep time. You can use the provided `InMemoryRetryStatistics` implementation or create your own.

### Retry and Recovery Callbacks

You can define custom logic to execute on each retry attempt and when all retries fail by implementing the `RetryCallback` and `RecoveryCallback` interfaces, respectively.

### RetryTemplate

The `RetryTemplate` class simplifies the process of executing operations with retry logic. You provide the operation logic and the RetryTemplate handles the retries according to the configured retry and backoff policies.

### Util

The `Sleep` class provides a static method for pausing execution for a specified number of milliseconds.

For more detailed documentation and examples, please refer to the inline comments in each class.

## License

RetryMaster is licensed under the MIT License. This means you can use and modify the code freely as long as you include the original copyright and permission notice in any copy of the software/source.

## Credits

RetryMaster is developed and maintained by @IlicMiljan. It's a product of many hours of hard work and dedication, and contributions from the open-source community are greatly appreciated.

This library is greatly inspired by the Spring Retry library, a part of the Spring Framework for Java. The design principles and structure of Spring Retry have been instrumental in shaping RetryMaster. If you're familiar with Spring Retry, you will find many similarities in RetryMaster.

Special thanks to the team behind the Spring Retry library for their impressive work, which serves as a foundation for this project. Their commitment to creating robust and flexible solutions for retry operations has been a significant inspiration.

Finally, a big thank you to all contributors and users of RetryMaster. Your feedback, bug reports, and feature suggestions are invaluable in making this library better. If you would like to contribute, please feel free to submit a pull request.
