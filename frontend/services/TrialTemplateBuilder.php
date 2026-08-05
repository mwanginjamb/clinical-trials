<?php

namespace frontend\services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Builds the "clinical_trials_batch_import_template.xlsx" workbook on the fly, so the
 * downloadable template always matches TrialBatchImporter::SHEET_COLUMNS below — there is
 * only one place (that const array) to update when a field is added or renamed.
 */
class TrialTemplateBuilder
{
    /**
     * @return Spreadsheet
     */
    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $this->buildInstructionsSheet($spreadsheet);

        foreach (TrialBatchImporter::SHEET_COLUMNS as $sheetName => $columns) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($sheetName);

            foreach (array_values($columns) as $col => $header) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
                $sheet->setCellValue("{$letter}1", $header);
            }

            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columns));
            $headerRange = "A1:{$lastCol}1";
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1F4E78');
            $sheet->getStyle('A1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('BF9000'); // trial_ref column stands out
            $sheet->freezePane('A2');
            foreach (range('A', $lastCol) as $letter) {
                $sheet->getColumnDimension($letter)->setWidth(22);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildInstructionsSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Instructions');
        $sheet->setCellValue('A1', 'Clinical Trials — Batch Upload Template');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->setCellValue('A3', 'Fill in the Trials sheet first, one row per trial, using a short '
            . 'trial_ref (T1, T2 …) in column A.');
        $sheet->setCellValue('A4', 'Then add rows to StudyPurposes, PopulationEligibility and Investigators, '
            . 'repeating the same trial_ref to link each row back to its trial.');
        $sheet->setCellValue('A5', 'Coded columns (registration_status, phase_of_study, country, city, role, '
            . 'etc.) take the numeric lookup ID from the system, not free text.');
        $sheet->getColumnDimension('A')->setWidth(110);
    }

    /**
     * Streams the workbook straight to the browser as an .xlsx download.
     */
    public function outputAsDownload(string $filename = 'clinical_trials_batch_import_template.xlsx'): void
    {
        $spreadsheet = $this->build();
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
