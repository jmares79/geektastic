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
    protected $totalPrices = array();

    public function __construct(
        FileReaderInterface $productsReader,
        FileReaderInterface $transactionsReader
    )
    {
        $this->productsReader = $productsReader;
        $this->transactionsReader = $transactionsReader;
    }

    public function getFetchedTransactions()
    {
        return $this->transactions['transactions'];
    }

    public function getFetchedProductsList()
    {
        return $this->products;
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

        $this->transactionsReader->closeStream();
    }

    /**
     * Convert an individual transaction parsed row for a better handling when processing prices
     *
     * @param string $transaction The transaction to be mapped and converted
     *
     * @return Sets the internal attribute with the converted transactions
     */
    protected function convertTransaction($transaction)
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

    public function calculateTotalPrice()
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction != null) {
                $this->totalPrices[] = $this->calculateTransactionPrice($transaction);
            }
        }
    }

    protected function calculateTransactionPrice($transaction)
    {
        $totalTransactionPrice = 0;

        foreach ($transaction as $product => $amount) {
            $totalProductPrice = 0;

            if (!$this->isValidProduct($product)) {
                continue;
            }

            $productInformation = $this->getLoadedMasterProduct($product);

            $individualPrice = $productInformation[1];
            $promotion = explode(" for ", $productInformation[2]);

            //No promotion
            if (count($promotion) == 1) {
                $totalProductPrice = $amount * $individualPrice; 
            } else {
                $promotionalAmount = $promotion[0];
                $quantityPrice = $promotion[1];

                $promotionalPrice = (int) ($amount / $promotionalAmount) * $quantityPrice;
                $normalPrice = ($amount % $promotionalAmount) * $individualPrice;

                $totalProductPrice = $promotionalPrice + $normalPrice;
            }

            $totalTransactionPrice += $totalProductPrice;
        }

        return $totalTransactionPrice;
    }

    protected function getLoadedMasterProduct($productName)
    {
        foreach ($this->products as $masterProduct) {
            if ($productName == $masterProduct[0]) {
                return $masterProduct;
            }
        } 
    }

    protected function isValidProduct($product)
    {
        foreach ($this->products as $position => $p) {
            if ($p[0] == $product) {
                return true;
            }
        }

        return false;
    }
}
