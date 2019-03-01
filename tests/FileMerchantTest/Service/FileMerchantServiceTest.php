<?php
use PHPUnit\Framework\TestCase;

use MerchantBundle\Service\FileMerchantService;
use CurrencyBundle\Service\CurrencyConverterService;
use StreamDataBundle\Service\StreamDataService;

class FileMerchantServiceTest extends TestCase
{
    const COUNT_ROWS = 5;
    const NO_DATA = 0;
    const ID = '2';

    protected $mockedConverter;
    protected $mockedStream;
    protected $merchant;

    public function setUp()
    {
        $this->mockedStream = $this->createMock(StreamDataService::class);
        $this->mockedConverter = $this->createMock(CurrencyConverterService::class);

        $this->merchant = new FileMerchantService($this->mockedConverter, $this->mockedStream);
    }

    /**
     * @dataProvider transactionsProvider
     */
    public function testFetchTransactions($merchantId, $provided, $count)
    {
        $this->mockedStream
            ->method('fetchData')
            ->willReturn($provided);

        $this->merchant->fetchTransactions($merchantId);
        $transactions = $this->merchant->getFetchedTransactions();

        $this->assertCount($count, $transactions);
    }

    public function transactionsProvider()
    {
        $transactions = array(
            'header' => array("merchant", "date", "value"),
            'transactions' => array(
                array("2", "01/05/2010", "£50.00"),
                array("2", "01/05/2010", "$66.10"),
                array("2", "02/05/2010", "€12.00"),
                array("2", "02/05/2010", "£6.50"),
                array("2", "04/05/2010", "€6.50"),
            )
        );

        $emptyTransactions = array(
            'header' => array("merchant", "date", "value"),
            'transactions' => array()
        );

        return array(
            array(self::ID, $transactions, self::COUNT_ROWS),
            array(self::ID, $emptyTransactions, self::NO_DATA)
        );
    }
}
