<?php

namespace StreamDataBundle\Service;

use MerchantBundle\Exceptions\FileParsingException;
use StreamDataBundle\Interfaces\StreamDataInterface;
use StreamDataBundle\Interfaces\FileReaderInterface;

/**
 *  Class for implementing a stream data fetcher
 */
class StreamDataService implements StreamDataInterface
{
    protected $reader;
    protected $transactions = [];

    public function __construct(FileReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Fetchs transactions from a stream
     *
     * @param int $merchantId
     * @param internal $reader Service
     *
     * @return An array with all the data to be shown
     */
    public function fetchData($merchantId)
    {
        $this->reader->openStream();

        $this->reader->parseHeader();
        $data = $this->getData($merchantId);

        $this->reader->closeStream();

        return $data;
    }

    /**
     * Get transaction information from a file row
     *
     * @param int $merchantId
     *
     * @return An array with the header and transactions
     */
    protected function getData($merchantId)
    {
        while ($row = $this->reader->getFileRow()) {
            $transaction = $this->reader->parseRow($row);

            if ($transaction[0] == $merchantId) {
                $this->transactions[] = $transaction;
            }
        }

        return [
            'header' => $this->reader->getHeader(),
            'transactions' => $this->transactions
        ];
    }
}
