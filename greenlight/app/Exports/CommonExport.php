<?php
/**
 * Created By Rativardhan Singh Sengar  7/30/19 12:16 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 7/29/19 9:32 PM
 */

namespace App\Exports;

use App\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;

class CommonExport implements FromArray
{
    protected $invoices;

    public function __construct(array $invoices)
    {
        $this->invoices = $invoices;
    }

    public function array(): array
    {
        return $this->invoices;
    }
}