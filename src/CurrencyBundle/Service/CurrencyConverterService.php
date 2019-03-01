<?php

namespace CurrencyBundle\Service;

use CurrencyBundle\Interfaces\CurrencyConverterInterface;
use CurrencyBundle\Interfaces\CurrencyExchangeRateInterface;

/**
 *  Class for implementing a currency converter, that wraps the logic for converting a currency
 */
class CurrencyConverterService implements CurrencyConverterInterface
{
    /**
     * @var The exchange rate service helper who will perform the conversion
     */
    protected $exchangeRateService;

    public function __construct(CurrencyExchangeRateInterface $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Converts an amount on a certain currency to GBP
     *
     * @param string $amount The amount with the specific currency
     * @param array $splitAmount Currency in position 0, amount in position 1
     *
     * @return A transaction with converted currency amounts
     */
    public function convert($amount)
    {
        $splitAmount = [];

        $splitAmount = $this->parseAmount($amount);
        $rateInfo = $this->exchangeRateService->getExchangeRate($splitAmount[0]);

        return $rateInfo['rate'] * $splitAmount[1];
    }

    /**
     * Parses an amount with its currency
     *
     * @param string $amount The amount with the specific currency
     * @param string $parsedAmount Array returned from the RegEx with the matched amounts & currency
     *
     * @return An array with the parsed currency and amount as preg_match_all formats them
     */
    protected function parseAmount($amount)
    {
        $parsedAmount = [];

        preg_match_all('/([\$|€|£])?(\d+\.\d+)/u', $amount, $parsedAmount, PREG_PATTERN_ORDER);

        return [$parsedAmount[1][0], $parsedAmount[2][0]];
    }
}
