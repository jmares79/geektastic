<?php

use PHPUnit\Framework\TestCase;

use ReportBundle\Service\OutputStdPrinterService;

class OutputStdPrinterServiceTest extends TestCase
{
    protected $printer;

    public function setUp()
    {
        $this->printer = new OutputStdPrinterService();
    }

    /**
     * @dataProvider fakeTransactionData
     */
    public function testShow($header, $transactions, $expected)
    {
        $this->expectOutputString($expected);

        $this->printer->show($header, $transactions);
    }

    /**
     *  Fake data to provide to the create report test
     */
    public function fakeTransactionData()
    {
        return array(
            array(
                array("merchant", "date", "value"),
                array(
                    array("2", "01/05/2010", "50.00"),
                    array("2", "01/05/2010", "50.897"),
                    array("2", "01/05/2010", "10.2"),
                    array("2", "01/05/2010", "6.5")
                ),
                'Merchant - Date - Value'.PHP_EOL.
                '2 - 01/05/2010 - £50.00'.PHP_EOL.
                '2 - 01/05/2010 - £50.90'.PHP_EOL.
                '2 - 01/05/2010 - £10.20'.PHP_EOL.
                '2 - 01/05/2010 - £6.50'.PHP_EOL
            )
        );
    }
}
