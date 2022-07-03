<?php

namespace App\Exports;

use App\Models\Sushi\FeePaidTable;
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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Traits\DateRange;
use Carbon\Carbon;

class FeePaidExport implements
    FromQuery,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithColumnFormatting,
    WithStyles,
    WithCustomStartCell,
    WithEvents
{
    use Exportable, DateRange;
    public $ids;
    public $sorts = [];
    public $tot_fee;
    public $tot_discount;

    public function __construct($ids, $sorts)
    {
        $this->ids = $ids;
        $this->sorts = $sorts;
    }


    public function query()
    {
        $sort_col = $sort_dir = "";

        foreach ($this->sorts as $sort_col => $sort_dir) {}

        $this->tot_fee = FeePaidTable::query()
            ->whereIn('receipt_id', $this->ids)
            ->selectraw('sum(amount) as tot_fee')->get()[0]->tot_fee;

        $this->tot_discount = FeePaidTable::query()
            ->whereIn('receipt_id', $this->ids)
            ->selectraw('sum(discount) as tot_discount')->get()[0]->tot_discount;

        $query = FeePaidTable::query()
            ->whereIn('receipt_id', $this->ids)
            ->select([
                'receipt_date',
                'admission_no',
                'full_name',
                'full_batch',
                'method',
                'amount',
                'discount',
                'fee_type',
                'receipt_id'
            ])
            ->orderby($sort_col, $sort_dir);
        return $query;
    }

    public function headings(): array
    {
        return [
            'Reciept Date',
            'Admission No',
            'Name',
            'Batch Name',
            'Method',
            'Amount',
            'Discount',
            'Type',
            'Receipt ID'
        ];
    }

    public function map($fee_paid): array
    {
        return [
            Carbon::createFromDate($fee_paid->receipt_date)->format('d-m-Y'),
            // $fee_paid->receipt_date,
            $fee_paid->admission_no,
            $fee_paid->full_name,
            $fee_paid->full_batch,
            $fee_paid->method,
            $fee_paid->amount,
            $fee_paid->discount,
            $fee_paid->fee_type,
            $fee_paid->receipt_id,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'G' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $this->setDateRange('fee_paid');
        $sheet->setCellValue("A1", 'Fee Paid Report [' . $this->start_date_dmy . ' - ' . $this->end_date_dmy . ']');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1:I2')->getFont()->setBold(true);

        $afterLastRow = $sheet->getHighestDataRow() + 1;
        $sheet->setCellValue('A' . $afterLastRow, 'Total');
        $sheet->setCellValue('F' . $afterLastRow, $this->tot_fee);
        $sheet->setCellValue('G' . $afterLastRow, $this->tot_discount);
        $range = 'A' . $afterLastRow . ':I' . $afterLastRow;
        $sheet->getStyle($range)->getFont()->setBold(true);
        $sheet->getStyle('F' . $afterLastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $sheet->getStyle('G' . $afterLastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    }

    public function startCell(): string
    {
        return 'A2';
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
