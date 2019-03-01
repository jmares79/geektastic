<?php
use PHPUnit\Framework\TestCase;

use CurrencyBundle\Service\StaticCurrencyExchangeRateService;
use CurrencyBundle\Service\CurrencyConverterService;

class CurrencyConverterServiceTest extends TestCase
{
    protected $mockedExchangeRateService;

    public function setUp()
    {
        $this->mockedExchangeRateService = $this->createMock(StaticCurrencyExchangeRateService::class);

        $this->currency = new CurrencyConverterService($this->mockedExchangeRateService);
    }

    /**
     * @dataProvider exchangeRateProvider
     */
    public function testConvertValidResults($amount, $rateInfo, $expected)
    {
        $this->mockedExchangeRateService
            ->method('getExchangeRate')
            ->willReturn($rateInfo);

        $converterAmount = $this->currency->convert($amount);

        $this->assertEquals($expected, $converterAmount);
    }

    /**
     * @dataProvider InvalidRateProvider
     */
    public function testConvertInvalidResults($amount, $rateInfo, $expected)
    {
        $this->mockedExchangeRateService
            ->method('getExchangeRate')
            ->willReturn($rateInfo);

        $converterAmount = $this->currency->convert($amount);

        $this->assertFalse($expected == $converterAmount);
    }

    public function exchangeRateProvider()
    {
        return array(
            array("£50.00", array('name' => "GBP", "rate" => 1), 50.00),
            array("$66.10", array('name' => "USD", "rate" => 0.77), 0.77 * 66.10),
            array("€12.00", array('name' => "EUR", "rate" => 0.85), 12.00 * 0.85),
        );
    }

    public function InvalidRateProvider()
    {
        return array(
            array("£50.00", array('name' => "GBP", "rate" => 1), 51.00),
            array("$66.10", array('name' => "USD", "rate" => 0.77), 0),
            array("€12.00", array('name' => "EUR", "rate" => 0.85), -12),
        );
    }
}
