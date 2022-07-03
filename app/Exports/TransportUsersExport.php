<?php

namespace App\Exports;

use App\Models\Sushi\TransportUsersTable;
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

class TransportUsersExport implements
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

        foreach ($this->sorts as $sort_col => $sort_dir) {}

        $query = TransportUsersTable::query()
            ->whereIn('id', $this->ids)
            ->select([
                'admission_no',
                'full_name',
                'aca_year',
                'full_batch',
                'bus_name',
                'bus_stop',
                'amount',
                's_date',
                'e_date'
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
            'Aca. Year',
            'Batch',
            'Bus Name',
            'Bus Stop',
            'Amount',
            'Start Date',
            'End Date'
        ];
    }

    public function map($transport_users): array
    {
        $data =  [
            $transport_users->admission_no,
            $transport_users->full_name,
            $transport_users->aca_year,
            $transport_users->full_batch,
            $transport_users->bus_name,
            $transport_users->bus_stop,
            $transport_users->amount,
            Carbon::createFromDate($transport_users->s_date)->format('d-m-Y'),

        ];

        if (!blank($transport_users->e_date))
            $data[] = Carbon::createFromDate($transport_users->e_date)->format('d-m-Y');
        return $data;
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue("A1", 'Transport Users List');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1:I2')->getFont()->setBold(true);
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
