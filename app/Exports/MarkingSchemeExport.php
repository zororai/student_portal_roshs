<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MarkingSchemeExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $assessment;
    protected $studentMarks;

    public function __construct($assessment, $studentMarks)
    {
        $this->assessment = $assessment;
        $this->studentMarks = $studentMarks;
    }

    public function collection()
    {
        $data = [];
        
        foreach ($this->studentMarks as $studentId => $studentData) {
            $row = [
                $studentData['name']
            ];
            
            // Add marks and comments for each paper
            foreach ($studentData['papers'] as $paperIndex => $paper) {
                $row[] = $paper['mark'];
                $row[] = $paper['comment'] ?? '';
            }
            
            $data[] = $row;
        }
        
        return collect($data);
    }

    public function headings(): array
    {
        $headings = ['Student Name'];
        
        // Get papers from assessment
        if ($this->assessment->papers) {
            foreach ($this->assessment->papers as $paper) {
                $headings[] = $paper['name'] . "\n" . 'Out of ' . $paper['weight'] . ' marks';
                $headings[] = 'Comment';
            }
        }
        
        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return substr($this->assessment->topic, 0, 31);
    }
}
