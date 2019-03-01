<?php

namespace ReportBundle\Service;

use ReportBundle\Interfaces\OutputReportInterface;

/**
 * Class for implementing the logic for showing data to stdout
 */
class OutputStdPrinterService implements OutputReportInterface
{
    const DECIMALS = 2;
    const SEPARATOR = ' - ';
    const GBP = '£';

    /**
     * Implements the show method from OutputReportInterface for showing the report
     *
     * @param mixed $header The header row of the stream/file
     * @param mixed $transactions The transactions from the stream/file
     *
     * @return Prints the data to stdout
     */
    public function show($header, $transactions)
    {
        $this->showHeader($header);
        $this->showTransactions($transactions);
    }

    /**
     * Shows the header of the stream/file
     *
     * @param mixed $header The header row of the stream/file
     *
     * @return Prints the header to stdout
     */
    protected function showHeader($header)
    {
        $names = count($header);

        for ($i = 0; $i < $names; $i++) {
            echo ucfirst($header[$i]);

            if ($i < $names-1) echo self::SEPARATOR;
        }

        echo PHP_EOL;
    }

    /**
     * Shows the transactions of the stream/file
     *
     * @param mixed $transactions The transactions of the stream/file
     *
     * @return Prints the transactions to stdout
     */
    protected function showTransactions($transactions)
    {
        foreach ($transactions as $transaction) {
            $transaction[2] = number_format($transaction[2], self::DECIMALS);
            $transaction[2] = self::GBP . $transaction[2];

            echo implode(self::SEPARATOR, $transaction);
            echo PHP_EOL;
        }
    }
}
