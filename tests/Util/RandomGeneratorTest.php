<?php

namespace IlicMiljan\RetryMaster\Tests\Util;

use IlicMiljan\RetryMaster\Util\RandomGenerator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RandomGeneratorTest extends TestCase
{
    public function testShouldGenerateNumberWithinRange(): void
    {
        $min = 5;
        $max = 10;

        $generator = new RandomGenerator();
        $randomNumber = $generator->nextInt($min, $max);

        // Check that the generated number is within the provided range.
        $this->assertGreaterThanOrEqual($min, $randomNumber);
        $this->assertLessThanOrEqual($max, $randomNumber);
    }
    public function testShouldGenerateSameNumberWhenMinEqualsMax(): void
    {
        $num = 7;

        $generator = new RandomGenerator();
        $randomNumber = $generator->nextInt($num, $num);

        // When min equals max, the generator should always return that number.
        $this->assertEquals($num, $randomNumber);
    }
}
