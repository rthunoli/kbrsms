<?php

namespace App\Http\Livewire;

use App\Exports\FeePaidHeadwiseExport;
use App\Models\Sushi\FeePaidHeadwiseTable as ModelsFeePaidHeadwiseTable;
use App\Models\Sushi\Feehead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Carbon\Carbon;
use App\Traits\CalcTotal;

class FeePaidHeadwiseTable extends DataTableComponent
{
    protected $model = ModelsFeePaidHeadwiseTable::class;
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
            // ->setFooterTrAttributes(
            //     function ($rows) {
            //         return [
            //             'default' => true,
            //             'class' => 'font-semibold'
            //         ];
            //         // return ['class' => 'bg-gray-200 font-semibold'];
            //     }
            // )
            // ->setDebugEnabled()
            ->setAdditionalSelects('id')
            ->setDefaultSort('receipt_date', 'desc')
            ->setPerPageAccepted([10, 25, 50, -1]);
        // ->setFooterDisabled()
        // ->setUseHeaderAsFooterEnabled()
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

            Column::make('Fee Head', 'feehead')
                ->searchable()
                ->sortable(),

            Column::make('Method', 'method')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('method'),

            Column::make('Amount', 'amount')
                // ->addClass('text-right')
                ->sortable()
                ->secondaryHeader(function () {
                    return $this->getTotal('amount');
                })
                ->format(fn ($value) => number_format($value, 2, '.', '')),

            Column::make('Discount', 'discount')
                ->searchable()
                ->sortable()
                ->secondaryHeader(function ($rows) {
                    return $this->getTotal('discount');
                })
                ->format(fn ($value) => number_format($value, 2, '.', '')),

            // Column::make('id', 'id')
            //     ->sortable(),
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
        // dd($ids);
        if ($ids == []) {
            $this->setAllSelected();
            $ids = $this->getSelected();
        }
        // dd($ids);

        $this->clearSelected();
        $sorts = [$this->getDefaultSortColumn() => $this->getDefaultSortDirection()];
        $sorts = $this->getSorts() == [] ? $sorts : $this->getSorts();
        // dd($sorts);
        return Excel::download(new FeePaidHeadwiseExport($ids, $sorts), 'feepaid-headwise.xlsx');
    }


    public function filters(): array
    {
        $feeheadids = $this->model::query()
            ->select('feehead_id')
            ->distinct()
            ->orderBy('feehead_id')
            ->get()
            ->keyBy('feehead_id')
            ->map(fn ($feehead_id) => $feehead_id->feehead_id)
            ->toarray();

        $methods = $this->model::query()
            ->select('method')
            ->orderBy('method_id')
            ->get()
            ->keyBy('method')
            ->map(fn ($method) => $method->method)
            ->toArray();

        $all_methods = Arr::prepend($methods, 'All', '');

        return [

            MultiSelectFilter::make('Feehead')
                ->options(
                    Feehead::query()
                        ->wherein('id', $feeheadids)
                        ->orderBy('id')
                        ->get()
                        ->keyBy('id')
                        ->map(fn ($feehead) => $feehead->name)
                        ->toArray()
                )->filter(function (Builder $builder, array $values) {
                    $builder->where('feehead_id', fn ($query) => $query->select('feehead_id')->whereIn('feehead_id', $values));
                }),
            // ->setFilterPillValues([
            //     '3' => 'Tag 1',
            // ]),

            SelectFilter::make('Method')
                ->setFilterPillTitle('Method')
                ->setFilterPillValues($methods)
                ->options($all_methods)
                ->filter(
                    function (Builder $builder, string $value) {
                        return $builder->where('method', $value);
                    }
                )
                ->hiddenFromAll(),
        ];
    }
}
