<?php

namespace App\Exports;
use App\Models\Sushi\TransportUnpaidTable;
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
use App\Traits\DateRange;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;

class TransportUnpaidExport implements 
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
    // public $tot_fee;
    // public $tot_discount;

    public function __construct($ids,$sorts) {
        // dd($ids);
        $this->ids = $ids;
        $this->sorts = $sorts;
    }

    public function query()
    {
        $sort_col = $sort_dir = "";

        foreach($this->sorts as $sort_col=>$sort_dir){}

        // $this->tot_fee = TransportUnpaidTable::query()
        //     ->whereIn('id', $this->ids)
        //     ->selectraw('sum(amount) as tot_fee')->get()[0]->tot_fee;

        // $this->tot_discount = TransportUnpaidTable::query()
        //     ->whereIn('id', $this->ids)
        //     ->selectraw('sum(discount) as tot_discount')->get()[0]->tot_discount;
        
        $query = TransportUnpaidTable::query()
            ->whereIn('id', $this->ids)
            ->select([
                'admission_no',
                'full_name',
                'full_batch',
                'startdate',
                'month',
            ])
            ->orderby($sort_col,$sort_dir);

        // dd($query->get());
        return $query;

    }

    public function headings():array
    {
        return [
            'Admission No',
            'Name',
            'Batch',
            'Start Date',
            'Month',
        ];
    }

    public function map($transport_unpaid):array
    {
        return [
            $transport_unpaid->admission_no,
            $transport_unpaid->full_name,
            $transport_unpaid->full_batch,
            Carbon::createFromDate($transport_unpaid->startdate)->format('d-m-Y'),
            $transport_unpaid->month,
        ];
    }

    public function columnFormats():array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function styles(Worksheet $sheet)
    {   
        $this->setDateRange('transport_unpaid');
        $sheet->setCellValue("A1",'Transport Unpaid [' . $this->start_date_dmy . ' - ' . $this->end_date_dmy . ']');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1:E2')->getFont()->setBold(true);
        
        // $afterLastRow=$sheet->getHighestDataRow()+1;
        // $sheet->setCellValue('A'.$afterLastRow,'Total');
        // $sheet->setCellValue('D'.$afterLastRow,$this->tot_fee);
        // $sheet->setCellValue('E'.$afterLastRow,$this->tot_discount);
        // $range='A'. $afterLastRow. ':E' . $afterLastRow;
        // $sheet->getStyle($range)->getFont()->setBold(true);
        // $sheet->getStyle('D'.$afterLastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        // $sheet->getStyle('E'.$afterLastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    }

    public function startCell():string
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
