<?php

namespace ReportBundle\Service;

use ReportBundle\Interfaces\OutputReportInterface;

/**
 * Class for implementing the logic for showing data to stdout
 */
class OutputStdPrinterService implements OutputReportInterface
{
    CONST HEADER = "TOTAL PRICE FOR THE CALCULATED TRANSACTIONS";
    /**
     * Implements the show method from OutputReportInterface for showing the report
     *
     * @param mixed $header The header row of the stream/file
     * @param mixed $transactions The transactions from the stream/file
     *
     * @return Prints the data to stdout
     */
    public function show($body, $header = null)
    {
        $this->showHeader($header);
        $this->showBody($body);
    }

    /**
     * Shows the header of the stream/file
     *
     * @param mixed $header The header row of the stream/file
     *
     * @return Prints the header to stdout
     */
    protected function showHeader($header = null)
    {
        if ($header == null) {
            echo self::HEADER;
        } else {
            echo $header;
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
    protected function showBody($body)
    {
        foreach ($body as $pos => $transaction) {
            $pos++;
            echo "$pos) $transaction \n";
        }
        
        echo PHP_EOL;
    }
}
