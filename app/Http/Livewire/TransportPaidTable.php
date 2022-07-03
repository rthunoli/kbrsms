<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Sushi\TransportPaidTable as ModelsTransportPaidTable;
use App\Traits\CalcTotal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Exports\TransportPaidExport;

class TransportPaidTable extends DataTableComponent
{
    protected $model = ModelsTransportPaidTable::class;
    use CalcTotal;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setSecondaryHeaderTrAttributes(function ($rows) {
                return [
                    // 'default' => true,
                    'class' => 'bg-gray-200 font-semibold'
                ];
            })
            // ->setDebugEnabled()
            ->setAdditionalSelects(['id'])
            ->setDefaultSort('admission_no')
            ->setPerPageAccepted([10, 25, 50, -1])
            // ->setFooterDisabled()
            // ->setUseHeaderAsFooterEnabled()
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Adm. No', 'admission_no')
                ->searchable()
                ->sortable()
                ->secondaryHeader(function ($rows) {
                    return 'Total :';
                }),

            Column::make('Name', 'full_name')
                ->searchable()
                ->sortable(),

            Column::make('Batch Name', 'full_batch')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('full_batch'),

            Column::make('Paid Date', 'date')
                ->format(fn ($value) => Carbon::createFromDate($value)->format('d-m-Y'))
                ->searchable()
                ->sortable(),

            Column::make('Month', 'month')
                ->searchable()
                ->sortable(),

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
        return Excel::download(new TransportPaidExport($ids, $sorts), 'transport-paid.xlsx');
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
        ];
    }
}
