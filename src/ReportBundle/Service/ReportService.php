<?php

namespace ReportBundle\Service;

use ReportBundle\Interfaces\OutputReportInterface;
use MerchantBundle\Interfaces\TransactionFetcherInterface;

class ReportService
{
    protected $outputPrinter;
    protected $merchantService;
    protected $transactions;

    public function __construct(
        OutputReportInterface $outputPrinter,
        TransactionFetcherInterface $merchantService
    )
    {
        $this->outputPrinter = $outputPrinter;
        $this->merchantService = $merchantService;
    }

    /**
     * Creates the report from the passed stream/file
     *
     * @return Fills the internal transactions attribute
     */
    public function createReport()
    {
        $this->merchantService->fetchTransactions();
        $this->merchantService->convertTransactions();
    }

    /**
     * Prints the report to stdout using the provided service
     *
     * @return void Prints data
     */
    public function printReport()
    {
        $this->outputPrinter->show(
            $this->merchantService->getHeader(),
            $this->merchantService->getConvertedTransactions()
        );
    }
}
