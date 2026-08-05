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
        // Reuse the sheet Spreadsheet() creates by default instead of removing it — deleting
        // the workbook's only sheet before adding replacements corrupts the active-sheet /
        // sheet-view bookkeeping in workbook.xml and is what triggers Excel's repair prompt.
        $spreadsheet = new Spreadsheet();
        $this->buildInstructionsSheet($spreadsheet->getActiveSheet());

        foreach (TrialBatchImporter::SHEET_COLUMNS as $sheetName => $columns) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($sheetName);

            foreach (array_values($columns) as $col => $header) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
                $sheet->setCellValue("{$letter}1", $header);
            }

            $colCount = count($columns);
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
            $headerRange = "A1:{$lastCol}1";
            $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1F4E78');
            $sheet->getStyle('A1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('BF9000'); // trial_ref column stands out
            $sheet->freezePane('A2');
            for ($i = 1; $i <= $colCount; $i++) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($letter)->setWidth(22);
            }

            // Example row (row 2), shaded green, trial_ref column shaded gold like the header.
            $example = self::EXAMPLE_ROWS[$sheetName] ?? [];
            foreach ($example as $i => $value) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue("{$letter}2", $value);
                $sheet->getStyle("{$letter}2")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($i === 0 ? 'FFF2CC' : 'E2EFDA');
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildInstructionsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('Instructions');
        $sheet->setShowGridlines(false);

        $sheet->setCellValue('A1', 'Clinical Trials — Batch Upload Template');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1F4E78');

        $sections = [
            ['heading', 'HOW THIS WORKBOOK IS ORGANISED'],
            ['text', 'This workbook has one sheet per database table that a trial can have several '
                . 'rows of, plus one main sheet for the trial and its one-to-one details.'],
            ['bullet', 'Trials — one row per trial (clinical_trial + the 1-to-1 tables: study_timeline, '
                . 'ethical_approval, funding, study_description, study_intervention, study_results, '
                . 'opendata_access)'],
            ['bullet', 'StudyPurposes — one row per study purpose/arm for a trial (a trial can have several)'],
            ['bullet', 'PopulationEligibility — one row per eligibility/population entry for a trial '
                . '(a trial can have several)'],
            ['bullet', 'Investigators — one row per investigator/team member for a trial (a trial can '
                . 'have several)'],
            ['spacer', ''],
            ['heading', "LINKING SHEETS TOGETHER — 'Trial Ref' COLUMN"],
            ['text', "Every sheet starts with a 'trial_ref' column, shaded gold. This is NOT a database "
                . 'ID — it is a temporary label you invent (e.g. T1, T2, T3 …) so the importer knows '
                . 'which StudyPurposes / PopulationEligibility / Investigators rows belong to which row '
                . 'on the Trials sheet.'],
            ['text', 'Use the exact same trial_ref on every child row that belongs to a given trial. The '
                . 'importer creates the clinical_trial record first, then uses the real generated '
                . 'trial_id to save the related rows.'],
            ['spacer', ''],
            ['heading', 'CODED / LOOKUP FIELDS'],
            ['text', 'Columns such as registration_status, area_of_specialization, type_of_study, '
                . 'phase_of_study, masking_status, control_group_name, funding_sector, country, city, '
                . 'role, recruiting_country, centre_region, publication_type and allow_publishing are '
                . 'stored as integer IDs referencing lookup tables in the system. Enter the numeric ID '
                . "exactly as it appears in the system's lookup/reference screens — do not type free "
                . 'text into these columns.'],
            ['spacer', ''],
            ['heading', 'DATES'],
            ['text', 'Enter dates as YYYY-MM-DD (e.g. 2026-09-01).'],
            ['spacer', ''],
            ['heading', 'REQUIRED FIELDS'],
            ['text', 'At minimum every Trials row needs: trial_ref, scientific_title, public_title. All '
                . 'other columns are optional at import time and can be completed later in the system.'],
            ['spacer', ''],
            ['heading', 'EXAMPLE ROWS'],
            ['text', 'A worked example (trial_ref = T1, shaded green) is provided on each data sheet — '
                . 'replace it with your data, or delete the row before importing a large batch.'],
        ];

        $row = 3;
        foreach ($sections as [$type, $text]) {
            if ($type === 'spacer') {
                $row++;
                continue;
            }
            $cell = $sheet->setCellValue("A{$row}", $type === 'bullet' ? "   •  {$text}" : $text);
            $style = $sheet->getStyle("A{$row}");
            if ($type === 'heading') {
                $style->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('1F4E78');
            } else {
                $style->getFont()->setSize(10);
            }
            $style->getAlignment()->setWrapText(true)->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            );
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getRowDimension($row)->setRowHeight(strlen($text) > 90 ? 30 : 15);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        foreach (['B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(16);
        }
    }

    /**
     * One shaded, self-explanatory sample row per sheet (trial_ref T1) so the workbook never
     * opens looking empty and so header-vs-data alignment is easy to eyeball.
     */
    private const EXAMPLE_ROWS = [
        'Trials' => [
            'T1', 'Efficacy of Drug X in Adult Asthma', 'Drug X for Asthma Study', 'DXAS-1', 'v1.0',
            '1', 'PRO-2026-001', 'REG-2026-0001', '3', '12',
            '24', 'Nairobi Hospital Site A', 'PO Box 100, Nairobi', '2026-09-01', '2027-09-01', '1',
            '1', '123 Hospital Rd, Nairobi', '1',
            '2', '1', 'ETH/2026/001', '/docs/eth_2026_001.pdf',
            'Acme Health Foundation', '150000', '1', '2',
            'https://trial-x.example.org', 'This study tests whether Drug X helps adults with asthma.',
            'A randomised, double-blind, placebo-controlled trial evaluating Drug X in adult asthma.',
            'Drug X 10mg', 'Oral tablet once daily for 12 weeks', 'Placebo tablet', '1',
            'Change in FEV1 at week 12',
            '1', 'Drug X reduced exacerbations by 30% vs placebo.', 'Institutional Review Board',
            'Journal of Respiratory Medicine', '10.1234/jrm.2027.001', '1',
            'Efficacy of Drug X in Adult Asthma: RCT',
            '1', 'Zenodo', 'trial_x_2026', 'Sensitivity analysis confirmed primary result', '128',
            'Bonferroni correction applied', 'Multiple imputation', 'GRADE', 'Low risk of bias',
            'Single-site limitation', 'None declared', 'Funnel plot symmetric', 'I2 = 12%', '0.95',
            'p<0.05', 'Mixed-effects regression',
        ],
        'StudyPurposes' => [
            'T1', 'Treatment', 'Assess efficacy of Drug X vs placebo in adult asthma',
            'Drug X reduces exacerbation frequency by >=25% vs placebo', '2', 'Drug X 10mg daily',
            '1', '1', '3', 'Block randomisation, 1:1', 'Double-blind: participants and assessors masked', '2',
        ],
        'PopulationEligibility' => ['T1', 'Adult persistent asthma', 'Inclusion', '200', '200', ''],
        'Investigators' => [
            'T1', '1', 'Nairobi Hospital', '1', 'Dr. Jane Mwangi', '+254700000000',
            'jane.mwangi@example.org', 'PO Box 100, Nairobi', '1',
        ],
    ];

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
