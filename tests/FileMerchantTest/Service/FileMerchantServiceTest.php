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

    /**
     * @dataProvider masterProductsProvider
     */
    public function testFetchProductsList($providedRow, $count)
    {
        $this->mockedProductReader
            ->method('parseRow')
            ->willReturn($providedRow);

        $this->merchant->fetchProductsList();
        $products = $this->merchant->getFetchedProductsList();

        $this->assertCount($count, $products);
    }

    /**
     * @dataProvider transactionsProvider
     */
    // public function testFetchTransactions($merchantId, $providedRow, $count)
    // {
    //     $this->mockedTransactionsReader
    //         ->method('parseRow')
    //         ->willReturn($providedRow);

    //     $this->merchant->fetchTransactions();
    //     $transactions = $this->merchant->getFetchedTransactions();

    //     $this->assertCount($count, $transactions);
    // }

    public function transactionsProvider()
    {
        $transactions = array(
            'header' => array("Products"),
            'transactions' => array(
                array("AAAA"),
                array("ABCDE"),
                array("XXXX"),
                array("EFFEEFG"),
                array("BDBAD"),
                array("AEEBABF"),
                array("A")
            )
        );

        $emptyTransactions = array(
            'header' => array("Products"),
            'transactions' => array()
        );

        return array(
            array($transactions, self::COUNT_ROWS),
            array($emptyTransactions, self::NO_DATA)
        );
    }

    public function masterProductsProvider()
    {
        $products = array(
            'header' => array("Item","Price","Offer"),
            'products' => array(
                array("A","     50","3 for 130"),
                array("B","     30","2 for 45"),
                array("C","     20",""),
                array("D","     15",""),
                array("E","     4","5 for 15"),
                array("F","     8","3 for 12"),
            )
        );

        $emptyProducts = array(
            'header' => array("Item","Price","Offer"),
            'products' => array()
        );

        return array(
            array($products, count($emptyProducts['products'])),
            array($emptyProducts, count($emptyProducts['products'])),
        );
    }
}
