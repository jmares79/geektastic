<?php

use Tests\SimpleTestCase;

use MerchantBundle\Service\FileMerchantService;
use StreamDataBundle\Service\FileReaderService;

class FileMerchantServiceTest extends SimpleTestCase
{
    const COUNT_ROWS = 7;
    const NO_DATA = 0;

    protected $mockedProductReader;
    protected $mockedTransactionsReader;
    protected $merchant;

    public function setUp()
    {
        $this->mockedProductReader = $this->createMock(FileReaderService::class);
        $this->mockedTransactionsReader = $this->createMock(FileReaderService::class);
        $this->merchant = new FileMerchantService($this->mockedProductReader, $this->mockedTransactionsReader);
    }

    protected function setMockedProductReaderBehaviour($getFileRow, $parseRow)
    {
        $this->mockedProductReader
            ->method('getFileRow')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $getFileRow));

         $this->mockedProductReader
            ->method('parseRow')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $parseRow));
    }

    protected function setMockedTransactionReaderBehaviour($values)
    {
        $this->mockedTransactionsReader
            ->method('getFileRow')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $values));

        $this->mockedTransactionsReader
            ->method('parseRow')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $values));
    }

    /**
     * @dataProvider masterProductsProvider
     */
    public function testFetchProductsList($getFileRow, $parseRow, $expectedCount)
    {
        $this->setMockedProductReaderBehaviour($getFileRow, $parseRow);

        $this->merchant->fetchProductsList();
        $products = $this->merchant->getFetchedProductsList();

        $this->assertCount($expectedCount, $products);
    }

    /**
     * @dataProvider transactionsProvider
     */
    public function testFetchTransactions($providedRow, $expectedCount)
    {
        $this->setMockedTransactionReaderBehaviour($providedRow);

        $this->merchant->fetchTransactions();
        $transactions = $this->merchant->getFetchedTransactions();
        // var_dump($transactions);
        $this->assertCount($expectedCount, $transactions);
    }

    /**
     * @dataProvider calculationProvider
     */
    public function testCalculateTotalPrice($products, $transactions, $expectedTotalPrices)
    {
        $this->setMockedProductReaderBehaviour($products['getFileRow'], $products['parseRow']);
        $this->setMockedTransactionReaderBehaviour($transactions);

        $this->merchant->fetchProductsList();
        $this->merchant->fetchTransactions();
        $this->merchant->calculateTotalPrice();

        $totalPrices = $this->merchant->getTotalPrices();

        $this->assertEquals($expectedTotalPrices[0], $totalPrices[0]);
        $this->assertEquals($expectedTotalPrices[1], $totalPrices[1]);
        $this->assertEquals($expectedTotalPrices[2], $totalPrices[2]);
        $this->assertEquals($expectedTotalPrices[3], $totalPrices[3]);
        $this->assertEquals($expectedTotalPrices[4], $totalPrices[4]);
        $this->assertEquals($expectedTotalPrices[5], $totalPrices[5]);
        $this->assertEquals($expectedTotalPrices[6], $totalPrices[6]);
    }

    public function masterProductsProvider()
    {
        // $products = array(
        //     'header' => array("Item","Price","Offer"),
        //     'getFileRow' => array(
        //         array("A;  50;3 for 130"),
        //         array("B;  30;2 for 45"),
        //         array("C;  20;"),
        //         array("D;  15;"),
        //         array("E;  4;5 for 15"),
        //         array(false),
        //     ),
        //     'parseRow' => array(
        //         array("A","50","3 for 130"),
        //         array("B","30","2 for 45"),
        //         array("C","20", ""),
        //         array("D","15", ""),
        //         array("E","4","5 for 15"),
        //         array(false),
        //     )
        // );
        $products = $this->getProductMockedData();

        return array(
            array($products['getFileRow'], $products['parseRow'], count($products['getFileRow']))
        );
    }

    public function transactionsProvider()
    {
        // $transactions = array(
        //     'transactions' => array(
        //         array("AAAA"),
        //         array("ABCDE"),
        //         array("XXXX"),
        //         array("EFFEEFG"),
        //         array("BDBAD"),
        //         array("AEEBABF"),
        //         array("A"),
        //         array(false),
        //     )
        // );

        $transactions = $this->getTransactionsMockedData();
        
        return array(
            array($transactions['transactions'], count($transactions['transactions']))
        );
    }

    public function calculationProvider()
    {
        $products = $this->getProductMockedData();

        /**
         * Transactions with total prices, which DEPENDS on the products loaded BEFORE!
         */
        $transactions = $this->getTransactionsMockedData();

        $totalPrices = array(180, 119, 0,12,125,153,50);

        return array(
            array($products, $transactions['transactions'], $totalPrices)
        );
    }
}
