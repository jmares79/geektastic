<?php
use PHPUnit\Framework\TestCase;

use CurrencyBundle\Service\StaticCurrencyExchangeRateService;

class StaticCurrencyExchangeRateServiceTest extends TestCase
{
    const USD_RATE = 0.77;
    const GBP_RATE = 1;
    const EUR_RATE = 0.85;

    protected $exchangeRate;

    public function setUp()
    {
        $this->exchangeRate = new StaticCurrencyExchangeRateService();
    }

    /**
     * @dataProvider exchangeRateProvider
     */
    public function testGetExchangeRateKeys($currency, $providedRate)
    {
        $rate = $this->exchangeRate->getExchangeRate($currency);

        $this->assertArrayHasKey('name', $rate);
        $this->assertArrayHasKey('rate', $rate);

        $this->assertEquals($providedRate['name'], $rate['name']);
        $this->assertEquals($providedRate['rate'], $rate['rate']);
    }

    public function exchangeRateProvider()
    {
        return array(
            array('$', ['name' => 'USD', 'rate' => self::USD_RATE]),
            array('£', ['name' => 'GBP', 'rate' => self::GBP_RATE]),
            array('€', ['name' => 'EUR', 'rate' => self::EUR_RATE]),
        );
    }


}
