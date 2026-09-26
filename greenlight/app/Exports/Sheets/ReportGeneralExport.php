<?php
namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReportGeneralExport implements FromArray, WithTitle,WithStyles
{
        protected $rows;
        protected $name;

        public function __construct(string $name,array $rows)
        {
                $this->rows = $rows; 
                $this->name = $name;
        }

        public function array(): array
        {
                return $this->rows;
        }
        public function title(): string
        {
                return $this->name;
        }

        public function styles(Worksheet $sheet)
        {
                return [
                // Style the first row as bold text.
                1    => ['font' => ['bold' => true]],
                ];
        }
        // public function columnWidths(): array
        // {
        //     return [
        //         'A' => 20,
        //     ];
        // }
}