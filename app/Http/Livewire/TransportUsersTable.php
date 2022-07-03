<?php

namespace App\Http\Livewire;

use App\Exports\TransportUsersExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Sushi\TransportUsersTable as ModelsTransportUsersTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

class TransportUsersTable extends DataTableComponent
{
    protected $model = ModelsTransportUsersTable::class;

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
                ->sortable(),

            Column::make('Name', 'full_name')
                ->searchable()
                ->sortable(),

            Column::make('Aca. Year', 'aca_year')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('aca_year'),

            Column::make('Batch Name', 'full_batch')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('full_batch'),

            Column::make('Bus Name', 'bus_name')
                ->searchable()
                ->sortable()
                ->secondaryHeaderFilter('bus_name'),

            Column::make('Bus Stop', 'bus_stop')
                ->searchable()
                ->sortable()
                ->format(fn ($value) => Str::title($value))
                ->secondaryHeaderFilter('bus_stop'),

            Column::make('Amount', 'amount')
                ->format(fn ($value) => number_format($value, 2, '.', '')),

            Column::make('Start Date', 's_date')
                ->format(fn ($value) => Carbon::createFromDate($value)->format('d-m-Y'))
                ->searchable()
                ->sortable(),

            Column::make('End Date', 'e_date')
                ->format(function ($value) {
                    if (!blank($value))
                        return Carbon::createFromDate($value)->format('d-m-Y');
                })
                ->searchable()
                ->sortable(),

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
        }
        $this->clearSelected();
        $sorts = [$this->getDefaultSortColumn() => $this->getDefaultSortDirection()];
        $sorts = $this->getSorts() == [] ? $sorts : $this->getSorts();
        return Excel::download(new TransportUsersExport($ids, $sorts), 'transport-users.xlsx');
    }

    public function filters(): array
    {
        $busstops = $this->model::query()
            ->distinct()
            ->select('bus_stop')
            ->orderBy('bus_stop')
            ->get()
            ->keyBy('bus_stop')
            ->map(fn ($bus_stop) => Str::title($bus_stop->bus_stop))
            ->toArray();

        $all_busstops = Arr::prepend($busstops, 'All', '');

        $busnames = $this->model::query()
            ->distinct()
            ->select('bus_name')
            ->orderBy('bus_name')
            ->get()
            ->keyBy('bus_name')
            ->map(fn ($bus_name) => $bus_name->bus_name)
            ->toArray();

        $all_busnames = Arr::prepend($busnames, 'All', '');

        $acayears = $this->model::query()
            ->distinct()
            ->select('aca_year')
            ->orderBy('aca_year', 'desc')
            ->get()
            ->keyBy('aca_year')
            ->map(fn ($acayear) => $acayear->aca_year)
            ->toArray();

        $all_acayears = Arr::prepend($acayears, 'All', '');

        $batches = $this->model::query()
            ->distinct()
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

            SelectFilter::make('AcaYear')
                ->setFilterPillTitle('AcaYear')
                ->setFilterPillValues($acayears)
                ->options($all_acayears)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('aca_year', $value);
                })
                ->hiddenFromAll(),

            SelectFilter::make('FullBatch')
                ->setFilterPillTitle('FullBatch')
                ->setFilterPillValues($batches)
                ->options($all_batches)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('full_batch', $value);
                })
                ->hiddenFromAll(),

            SelectFilter::make('BusName')
                ->setFilterPillTitle('BusName')
                ->setFilterPillValues($busnames)
                ->options($all_busnames)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('bus_name', $value);
                })
                ->hiddenFromAll(),

            SelectFilter::make('BusStop')
                ->setFilterPillTitle('BusStop')
                ->setFilterPillValues($busstops)
                ->options($all_busstops)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('bus_stop', $value);
                })
                ->hiddenFromAll(),
        ];
    }
}
