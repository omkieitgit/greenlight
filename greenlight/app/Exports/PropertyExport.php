<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\ReportGeneralExport;
use App\Exports\Sheets\ReportLeadsExport;

class PropertyExport implements FromArray, WithMultipleSheets
{
    protected $sheets;

    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
       
    }

    public function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {

        foreach($this->sheets as $sheetName => $data){
                $sheets[]=  new ReportGeneralExport($sheetName,$data);
        }
       
        return $sheets;
    }
}
