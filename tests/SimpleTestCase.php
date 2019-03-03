<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class SimpleTestCase extends TestCase
{
    protected function getProductMockedData()
    {
        return array(
            'header' => array("Item","Price","Offer"),
            'getFileRow' => array(
                array("A;  50;3 for 130"),
                array("B;  30;2 for 45"),
                array("C;  20;"),
                array("D;  15;"),
                array("E;  4;5 for 15"),
                array(false),
            ),
            'parseRow' => array(
                array("A","50","3 for 130"),
                array("B","30","2 for 45"),
                array("C","20", ""),
                array("D","15", ""),
                array("E","4","5 for 15"),
                array(false),
            )
        );
    }

    protected function getTransactionsMockedData()
    {
        return array(
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
    }
}