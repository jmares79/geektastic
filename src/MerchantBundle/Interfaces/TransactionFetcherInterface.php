<?php

namespace MerchantBundle\Interfaces;

interface TransactionFetcherInterface
{
    public function fetchTransactions($merchantId);
}
