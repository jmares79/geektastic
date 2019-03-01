<?php

namespace CurrencyBundle\Interfaces;

interface CurrencyExchangeRateInterface
{
    public function getExchangeRate($currency);
}
