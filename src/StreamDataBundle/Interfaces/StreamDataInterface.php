<?php

namespace StreamDataBundle\Interfaces;

/**
 *  Interface for a stream data fetcher
 */
interface StreamDataInterface
{
    public function fetchData($merchantId);
}
