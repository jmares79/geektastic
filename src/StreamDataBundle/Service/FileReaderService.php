<?php

namespace StreamDataBundle\Service;

use MerchantBundle\Exceptions\FileParsingException;
use StreamDataBundle\Interfaces\FileReaderInterface;

/**
 *  Class for implementing a stream data fetcher
 */
class FileReaderService implements FileReaderInterface
{
    const READ_MODE = 'r';

    protected $streamDirectory;
    protected $streamName;
    protected $handler;

    public function __construct($streamDirectory, $streamName)
    {
        $this->streamDirectory = $streamDirectory;
        $this->streamName = $streamName;
    }

    public function setStreamDirectory($dir)
    {
        $this->setStreamDirectory = $dir;
    }

    public function setStreamPath($path)
    {
        $this->setStreamPath = $path;
    }

    public function openStream()
    {
        if (!$this->handler = fopen($this->getFullPath(), self::READ_MODE)) throw new FileParsingException();
    }

    public function closeStream()
    {
        fclose($this->handler);
    }

    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Parses the header of the stream
     *
     * @return An array with the header data
     */
    public function parseHeader()
    {
        $headerRow = fgetcsv($this->handler);
        $this->header = $this->parseRow($headerRow);
    }

    /**
     * Parses and return a row of the stream
     *
     * @return An array with the row data
     */
    public function getFileRow()
    {
        return fgetcsv($this->handler);
    }

    /**
     * Parses a common row of the stream
     *
     * @param mixed $row
     *
     * @return An array with the row data
     */
    public function parseRow($row)
    {
        return explode(";", str_replace("\"", "", $row[0]));
    }

    protected function getFullPath()
    {
        return $this->streamDirectory . $this->streamName;
    }
}
