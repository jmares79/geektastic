<?php
use PHPUnit\Framework\TestCase;

use MerchantBundle\Service\FileMerchantService;
use StreamDataBundle\Service\FileReaderService;

class FileMerchantServiceTest extends TestCase
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

    protected function setMockedProductReaderBehaviour($values)
    {
        var_dump($values);
        $this->mockedProductReader
            ->method('getFileRow')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $values));
            // ->will($this->onConsecutiveCalls(array("A;  4;3 for 8"), array("F;  8;3 for 12"), false));

         $this->mockedProductReader
            ->method('parseRow')
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $values));
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
    public function testFetchProductsList($providedRow, $expectedCount)
    {
        $this->setMockedProductReaderBehaviour($providedRow);

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

        $this->assertCount($expectedCount, $transactions);
    }

    // public function testCalculateTotalPrice($providedRow, $expectedCount)
    // {
    //     $this->setMockedProductReaderBehaviour();
    //     $this->setMockedTransactionReaderBehaviour();

    //     $this->merchant->fetchProductsList();
    //     $this->merchant->fetchTransactions();
    //     $this->merchant->calculateTotalPrice();

    //     $totalPrices = $this->merchant->getTotalPrices();

    //     $this->assertEquals(12, $totalPrices[0]);
    //     $this->assertEquals(4, $totalPrices[1]);
    // }

    public function transactionsProvider()
    {
        $transactions = array(
            'transactions' => array(
                array("AAAA"),
                array("ABCDE"),
                array("XXXX"),
                array("EFFEEFG"),
                array("BDBAD"),
                array("AEEBABF"),
                array("A"),
                array(false),
            )
        );

        return array(
            array($transactions['transactions'], count($transactions['transactions']))
        );
    }

    public function masterProductsProvider()
    {
        $products = array(
            'header' => array("Item","Price","Offer"),
            'products' => array(
                array("A;  50;3 for 130"),
                array("B;  30;2 for 45"),
                array("C;  20;"),
                array("D;  15;"),
                array("E;  4;5 for 15"),
                array(false),
            )
        );

        $emptyProducts = array(
            'header' => array("Item","Price","Offer"),
            'products' => array()
        );

        return array(
            array($products['products'], count($products['products']))
        );
    }
}
