<?php

namespace CurrencyBundle\Interfaces;

interface CurrencyConverterInterface
{
    public function convert($amount);
}
