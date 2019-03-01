<?php

namespace MerchantBundle\Interfaces;

interface TransactionFetcherInterface
{
    public function fetchProductsList();
    public function fetchTransactions();
}
