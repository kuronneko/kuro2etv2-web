<?php

namespace App\Exports;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class FilamentFile2esExcelExport extends ExcelExport
{
    public function registerEvents(): array
    {
        $events = parent::registerEvents();

        $events[AfterSheet::class] = function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Read header row to find the "Obfuscated text" column (case-insensitive contains 'text')
            $headerRange = 'A1:' . $highestColumn . '1';
            $headers = $sheet->rangeToArray($headerRange)[0] ?? [];

            $textColIndex = null;
            foreach ($headers as $idx => $heading) {
                if ($heading && stripos((string) $heading, 'text') !== false) {
                    $textColIndex = $idx; // 0-based
                    break;
                }
            }

            if ($textColIndex === null) {
                return [];
            }

            $colLetter = Coordinate::stringFromColumnIndex($textColIndex + 1);

            for ($row = 2; $row <= $highestRow; $row++) {
                $cell = $colLetter . $row;
                $value = $sheet->getCell($cell)->getValue();
                // Write value explicitly as string to avoid Excel numeric parsing
                $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
            }
        };

        return $events;
    }
}
