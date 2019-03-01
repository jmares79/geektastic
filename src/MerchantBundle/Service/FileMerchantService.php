<?php

namespace MerchantBundle\Service;

use MerchantBundle\Interfaces\TransactionFetcherInterface;
use StreamDataBundle\Interfaces\StreamDataInterface;
use StreamDataBundle\Interfaces\FileReaderInterface;

/**
 * Service that fetches transactions from a CSV file
 */
class FileMerchantService implements TransactionFetcherInterface
{
    protected $productsReader;
    protected $transactionsReader;
    protected $products = array();
    protected $transactions = array();

    public function __construct(
        FileReaderInterface $productsReader,
        FileReaderInterface $transactionsReader
    )
    {
        $this->productsReader = $productsReader;
        $this->transactionsReader = $transactionsReader;
    }

    // public function getHeader()
    // {
    //     return $this->transactions['header'];
    // }

    public function getFetchedTransactions()
    {
        return $this->transactions['transactions'];
    }

    /**
     * Fetches the product list from a file source, returning a list of them
     *
     * @return void Loads internal structure with the products list
     */
    public function fetchProductsList()
    {
        $this->productsReader->openStream();
        $this->productsReader->parseHeader();

        while ($row = $this->productsReader->getFileRow()) {
            $this->products[] = $this->productsReader->parseRow($row);
        }

        $this->productsReader->closeStream();

        return [
            'products' => $this->products
        ];
    }

    /**
     * Fetches the transactions from a file source, returning a list of them
     *
     * @return An array with the converted transactions
     */
    public function fetchTransactions()
    {
        $this->transactionsReader->openStream();
        $this->transactionsReader->parseHeader();

        while ($row = $this->transactionsReader->getFileRow()) {
            $rawTransaction = $this->transactionsReader->parseRow($row);
            // var_dump($rawTransaction[0]);

            if (!is_array($rawTransaction)) {
                throw new \Exception("Error processing transaction. Must be an array", 1);
            }

            $t = str_split($rawTransaction[0]);
            sort($t);
            $transaction = implode($t);

            if ($transaction != '') {
                $convertedTransaction = $this->convertTransaction($transaction);
            } else {
                $convertedTransaction = null;
            }

            $this->transactions[] = $convertedTransaction;
        }

        var_dump($this->transactions);
        die;

        $this->transactionsReader->closeStream();

        return [
            'transactions' => $this->transactions
        ];
    }

    /**
     * Convert an individual transaction parsed row for a better handling when processing prices
     *
     * @param string $transaction The transaction to be mapped and converted
     *
     * @return Sets the internal attribute with the converted transactions
     */
    public function convertTransaction($transaction)
    {
        $converted = [];
        $amount = 1;
        $currentChar = $transaction[0];

        for ($i = 0; $i < strlen($transaction); $i++) {
            if ($transaction[$i] != $currentChar) {
                $amount = 1;
                $currentChar = $transaction[$i];
            }

            $converted[$transaction[$i]] = $amount++;

        }

        return $converted;
    }

    public function getConvertedTransactions()
    {
        return $this->converted;
    }
}
