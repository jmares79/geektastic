<?php
use PHPUnit\Framework\TestCase;

use StreamDataBundle\Service\StreamDataService;
use StreamDataBundle\Service\FileReaderService;

class StreamDataServiceTest extends TestCase
{
    const SINGLE_ROW = 1;
    const NO_DATA = 0;
    const ID = '2';

    protected $stream;
    protected $mockedReader;

    public function setUp()
    {
        $this->mockedReader = $this->createMock(FileReaderService::class);
        $this->mockedReader->method('getHeader')->willReturn('"merchant";"date";"value"');

        $this->stream = new StreamDataService($this->mockedReader);
    }

    /**
     * @dataProvider emptyRowProvider
     */
    public function testTransactionHeadersAreCreated($merchantId)
    {
        $this->mockedReader->method('getFileRow')->willReturn(false);

        $this->assertArrayHasKey('header', $this->stream->fetchData($merchantId));
        $this->assertArrayHasKey('transactions', $this->stream->fetchData($merchantId));
    }

    /**
     * @dataProvider rowProvider
     */
    public function testFetchTransactionsWithData($merchantId, $count)
    {
        $this->mockedReader->method('getFileRow')->will($this->onConsecutiveCalls(array('2;"01/05/2010";"£50.00"'), false));
        $this->mockedReader->method('parseRow')->willReturn(array("2", "01/05/2010", "£50.00"));

        $fetchedData = $this->stream->fetchData($merchantId);

        $this->assertCount($count, $fetchedData['transactions']);
    }

    public function emptyRowProvider()
    {
        return array(
            array(self::ID)
        );
    }

    public function rowProvider()
    {
        return array(
            array(self::ID, self::SINGLE_ROW)
        );
    }
}
