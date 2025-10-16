<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhpSpreadsheetExporter
{
    /**
     * Stream given rows as XLSX
     * @param string $filename
     * @param array $headers
     * @param iterable $rows
     * @return StreamedResponse
     */
    public function streamXlsx(string $filename, array $headers, iterable $rows)
    {
        $response = new StreamedResponse(function() use ($headers, $rows) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // headers
            $col = 1;
            foreach ($headers as $h) {
                $sheet->setCellValueByColumnAndRow($col++, 1, $h);
            }

            $rowNum = 2;
            foreach ($rows as $r) {
                $col = 1;
                foreach ($headers as $key => $h) {
                    $value = is_array($r) ? ($r[$key] ?? '') : (property_exists($r, $key) ? $r->$key : '');
                    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $value);
                }
                $rowNum++;
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $disposition = 'attachment; filename="' . $filename . '"';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * Stream given rows as CSV
     */
    public function streamCsv(string $filename, array $headers, iterable $rows, string $delimiter = ',')
    {
        $response = new StreamedResponse(function() use ($headers, $rows, $delimiter) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers, $delimiter);
            foreach ($rows as $r) {
                $line = [];
                foreach ($headers as $key => $h) {
                    $value = is_array($r) ? ($r[$key] ?? '') : (property_exists($r, $key) ? $r->$key : '');
                    $line[] = $value;
                }
                fputcsv($out, $line, $delimiter);
            }
            fclose($out);
        });

        $disposition = 'attachment; filename="' . $filename . '"';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
