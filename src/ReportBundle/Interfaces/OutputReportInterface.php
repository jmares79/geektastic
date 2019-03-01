<?php

namespace ReportBundle\Interfaces;

interface OutputReportInterface
{
    public function show($header, $transactions);
}
