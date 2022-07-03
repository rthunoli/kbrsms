<?php

namespace App\Exports;

use App\Models\Sushi\SearchStudentsTable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
// use App\Traits\DateRange;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;

class StudentsExport implements
    FromQuery,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithColumnFormatting,
    WithStyles,
    WithCustomStartCell,
    WithEvents
{
    use Exportable;
    // use DateRange;
    public $ids;
    public $sorts = [];

    public function __construct($ids, $sorts)
    {
        // dd($ids);
        $this->ids = $ids;
        $this->sorts = $sorts;
    }

    public function query()
    {
        $sort_col = $sort_dir = "";

        foreach ($this->sorts as $sort_col => $sort_dir) {
        }

        $query = SearchStudentsTable::query()
            ->whereIn('id', $this->ids)
            ->select([
                'admission_no',
                'full_name',
                'gender',
                'status',
                'full_batch',
                'medium',
                'second_language',
                'digital',
                'phone_no',
                'phone_home',
                'phone_father',
                'admitted_class',
                'admission_date',
                'dob',
                'father_name',
                'father_occupation',
                'mother_name',
                'mother_occupation',
                'phone_mother',
                'guardian_name',
                'phone_guardian',
            ])
            ->orderby($sort_col, $sort_dir);

        // dd($query->get());
        return $query;
    }

    public function headings(): array
    {
        return [
            'Admission No',
            'Name',
            'Gender',
            'Status',
            'Batch',
            'Medium',
            'First Lang.',
            'Digital',
            'Contact Phone',
            'Home Phone',
            'Father\'s Phone',
            'Adm. Class',
            'Adm. Date',
            'Date of Birth',
            'Name of Father',
            'Occup. of Father',
            'Name of Mother',
            'Occup. of Mother',
            'Mother\'s Phone',
            'Name of Guardian',
            'Guardian\'s Phone'
        ];
    }

    public function map($students): array
    {
        $adm_date='';
        $dob='';
        if (!blank($students->admission_date))
            $adm_date = Carbon::createFromDate($students->admission_date)->format('d-m-Y');

        if (!blank($students->dob))
            $dob = Carbon::createFromDate($students->dob)->format('d-m-Y');

        $data =  [
            $students->admission_no,
            $students->full_name,
            $students->gender,
            $students->status,
            $students->full_batch,
            $students->medium,
            $students->second_language,
            $students->digital,
            $students->phone_no,
            $students->phone_home,
            $students->phone_father,
            $students->admitted_class,
            $adm_date,
            $dob,
            $students->father_name,
            $students->father_occupation,
            $students->mother_name,
            $students->mother_occupation,
            $students->phone_mother,
            $students->guardian_name,
            $students->phone_guardian
        ];

        return $data;
    }

    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue("A1", 'Students List');
        $sheet->mergeCells('A1:U1');
        $sheet->getStyle('A1:U2')->getFont()->setBold(true);
    }

    public function startCell(): string
    {
        return "A2";
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $sheet->getProtection()->setPassword('Amar@3666');
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setSort(true);
                $sheet->getProtection()->setInsertRows(true);
                $sheet->getProtection()->setFormatCells(true);
            }
        ];
    }
}
