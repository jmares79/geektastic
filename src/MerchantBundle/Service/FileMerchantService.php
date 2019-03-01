<?php

namespace MerchantBundle\Service;

use MerchantBundle\Interfaces\TransactionFetcherInterface;
use CurrencyBundle\Interfaces\CurrencyConverterInterface;
use StreamDataBundle\Interfaces\StreamDataInterface;

/**
 * Service that fetches transactions from a CSV file
 */
class FileMerchantService implements TransactionFetcherInterface
{
    protected $converter;
    protected $stream;
    protected $data;
    protected $converted;

    public function __construct(
        CurrencyConverterInterface $converter,
        StreamDataInterface $stream
    )
    {
        $this->converter = $converter;
        $this->stream = $stream;
    }

    public function getHeader()
    {
        return $this->data['header'];
    }

    public function getFetchedTransactions()
    {
        return $this->data['transactions'];
    }
    /**
     * Fetches the transaction from a file source, returning a list of them
     *
     * @param int $merchantId
     *
     * @return An array with the converted transactions
     */
    public function fetchTransactions($merchantId = null)
    {
        $this->data = $this->stream->fetchData($merchantId);
    }

    /**
     * Convert the transactions by exchanging the amount to the base currency
     *
     * @param internal $data
     *
     * @return Sets the internal attribute with the converted transactions
     */
    public function convertTransactions()
    {
        $converted = [];

        foreach ($this->data['transactions'] as $transaction) {
            list($id, $date, $amount) = $transaction;

            $this->converted[] = [$id, $date, $this->converter->convert($amount)];
        }
    }

    public function getConvertedTransactions()
    {
        return $this->converted;
    }
}
