<?php

namespace App\Http\Livewire;

use App\Exports\FeePaidExport;
use App\Models\Sushi\FeePaidTable as ModelsFeePaidTable;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Carbon;
use App\Traits\CalcTotal;
use Illuminate\Support\Arr;

class FeePaidTable extends DataTableComponent
{
    protected $model = ModelsFeePaidTable::class;
    use CalcTotal;

    public function configure(): void
    {

        $this->setPrimaryKey('receipt_id')
            ->setSecondaryHeaderTrAttributes(function ($rows) {
                return [
                    // 'default' => true,
                    'class' => 'bg-gray-200 font-semibold'
                ];
            })
            // ->setDebugEnabled()
            ->setDefaultSort('receipt_date', 'desc')
            ->setPerPageAccepted([10, 25, 50, -1])
            // ->setFooterDisabled()
            // ->setUseHeaderAsFooterEnabled()
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Rcpt. Date', 'receipt_date')
                ->searchable()
                ->sortable()
                ->secondaryHeader(function ($rows) {
                    return 'Total :';
                })
                ->format(fn ($value) => Carbon::createFromDate($value)->format('d-m-Y')),

            Column::make('Adm. No.', 'admission_no')
                ->searchable()
                ->sortable(),

            Column::make('Name', 'full_name')
                ->searchable()
                ->sortable(),

            Column::make('Batch Name', 'full_batch')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('full_batch'),

            Column::make('Method', 'method')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('method'),

            Column::make('Amount', 'amount')
                ->sortable()
                ->secondaryHeader(function () {
                    return $this->getTotal('amount');
                })
                ->format(fn ($value) => number_format($value, 2, '.', '')),

            Column::make('Discount', 'discount')
                ->sortable()
                ->secondaryHeader(function () {
                    return $this->getTotal('discount');
                })
                ->format(fn ($value) => number_format($value, 2, '.', '')),

            Column::make('Fee Type', 'fee_type')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('fee_type'),

            Column::make('Rcpt. No.', 'receipt_id')
                ->searchable()
                ->sortable()
        ];
    }

    public function bulkActions(): array
    {
        return [
            'export' => 'Export',
        ];
    }

    public function export()
    {
        $ids = $this->getSelected();
        if ($ids == []) {
            $this->setAllSelected();
            $ids = $this->getSelected();
            // dd($ids);
        }
        $this->clearSelected();
        $sorts = [$this->getDefaultSortColumn() => $this->getDefaultSortDirection()];
        $sorts = $this->getSorts() == [] ? $sorts : $this->getSorts();
        // dd($sorts);
        return Excel::download(new FeePaidExport($ids, $sorts), 'feepaid.xlsx');
    }


    public function filters(): array
    {
        $batches = $this->model::query()
            ->select('full_batch')
            ->orderBy('class_id')
            ->orderBy('full_batch')
            ->get()
            ->keyBy('full_batch')
            ->map(fn ($batch) => $batch->full_batch)
            ->toArray();

        $all_batches = Arr::prepend($batches, 'All', '');


        $methods = $this->model::query()
            ->select('method')
            ->orderBy('method_id')
            ->get()
            ->keyBy('method')
            ->map(fn ($method) => $method->method)
            ->toArray();

        $all_methods = Arr::prepend($methods, 'All', '');

        return [

            MultiSelectFilter::make('Class')
                ->options(
                    $this->model::query()
                        ->select('class_id', 'class_name')
                        ->orderBy('class_id')
                        ->get()
                        ->keyBy('class_id')
                        ->map(fn ($course) => $course->class_name)
                        ->toArray()
                )
                ->filter(function (Builder $builder, array $values) {
                    $builder->where(
                        'class_id',
                        fn ($query) => $query->select('class_id')->whereIn('class_id', $values)
                    );
                }),

            SelectFilter::make('FullBatch')
                ->setFilterPillTitle('FullBatch')
                ->setFilterPillValues($batches)
                ->options($all_batches)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('full_batch', $value);
                })
                ->hiddenFromAll(),

            SelectFilter::make('Method')
                ->setFilterPillTitle('Method')
                ->setFilterPillValues($methods)
                ->options($all_methods)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('method', $value);
                })
                ->hiddenFromAll(),

            SelectFilter::make('FeeType')
                ->setFilterPillTitle('FeeType')
                ->setFilterPillValues([
                    'Academic' => 'Academic',
                    'Admission' => 'Admission'
                ])
                ->options([
                    '' => 'All',
                    'Academic' => 'Academic',
                    'Admission' => 'Admission'
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('fee_type', $value);
                })
                ->hiddenFromAll(),

        ];
    }


    // public function refresh()
    // {
    //     $this->emit('refreshDatatable');
    // }

}
