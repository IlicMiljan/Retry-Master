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

- `AlwaysRetryPolicy`: This policy will always retry the operation, regardless of the type of exception.
- `MaxAttemptsRetryPolicy`: This policy will retry the operation up to a maximum number of attempts.
- `NeverRetryPolicy`: This policy will never retry the operation.
- `NonRepeatingExceptionRetryPolicy`: This policy will retry the operation if the last exception is of a different type than the current exception.
- `SimpleRetryPolicy`: This policy will retry the operation a specified number of times and for specific exceptions.
- `SpecificExceptionRetryPolicy`: This policy will retry the operation if the exception is an instance of a specific class.
- `TimeoutRetryPolicy`: This policy will retry the operation as long as the elapsed time is less than a specified timeout.

You can also create your own retry policies by implementing the `RetryPolicy` interface.

### Backoff Policies

A backoff policy determines the delay between retry attempts. RetryMaster includes several built-in backoff policies, such as:

- `FixedBackoffPolicy`: This policy applies a fixed delay between retry attempts.
- `NoBackoffPolicy`: This policy applies no delay between retry attempts.
- `UniformRandomBackoffPolicy`: This policy applies a random delay (within a specified range) between retry attempts.

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
