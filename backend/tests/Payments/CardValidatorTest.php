<?php

use App\Payments\CardValidator;
use PHPUnit\Framework\TestCase;

class CardValidatorTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new CardValidator();
    }

    public function testValidCardNumberPasses()
    {
        $result = $this->validator->isValidCardNumber(
            '4242424242424242'
        );

        $this->assertTrue($result);
    }

    public function testCardNumberWithSpacesPasses()
    {
        $result = $this->validator->isValidCardNumber(
            '4242 4242 4242 4242'
        );

        $this->assertTrue($result);
    }

    public function testInvalidCardNumberFails()
    {
        $result = $this->validator->isValidCardNumber(
            '1234567890123456'
        );

        $this->assertFalse($result);
    }

    public function testRepeatedDigitCardFails()
    {
        $result = $this->validator->isValidCardNumber(
            '1111111111111111'
        );

        $this->assertFalse($result);
    }

    public function testValidFutureExpiryPasses()
    {
        $result = $this->validator->isValidExpiry('12/99');

        $this->assertTrue($result);
    }

    public function testExpiredCardFails()
    {
        $result = $this->validator->isValidExpiry('01/20');

        $this->assertFalse($result);
    }

    public function testInvalidExpiryFormatFails()
    {
        $result = $this->validator->isValidExpiry('13/30');

        $this->assertFalse($result);
    }

    public function testThreeDigitCvvPasses()
    {
        $this->assertTrue(
            $this->validator->isValidCvv('123')
        );
    }

    public function testFourDigitCvvPasses()
    {
        $this->assertTrue(
            $this->validator->isValidCvv('1234')
        );
    }

    public function testInvalidCvvFails()
    {
        $this->assertFalse(
            $this->validator->isValidCvv('12a')
        );
    }
}
