<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Sushi\SearchStudentsTable as ModelsSearchStudentsTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Exports\StudentsExport;

class SearchStudentsTable extends DataTableComponent
{
    protected $model = ModelsSearchStudentsTable::class;


    private function getFirstLangCount()
    {
        $ids = $this->getSelected();
        if ($ids == []) {
            $this->setAllSelected();
            $ids = $this->getSelected();
            // dump($ids);
            $this->clearSelected();
        }

        $lang_count =  $this->model::query()
            ->whereIn($this->getPrimaryKey(), $ids)
            ->select('second_language')
            ->selectraw('count(*) as count')
            ->groupby('second_language')
            ->get()
            ->keyby('second_language')
            ->map(fn ($row) => $row->count)
            ->toarray();

        return $lang_count;
    }

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
            ->setPerPageAccepted([5, 10, 25, 50, -1])
            // ->setFooterDisabled()
            // ->setUseHeaderAsFooterEnabled()
        ;
    }

    public function columns(): array
    {
        return [

            // Column::callback('a.id,a.admission_no',function($id,$admno){
            //     $subdomain = Str::lower(session('db'));
            //     $url = "https://$subdomain.kadamburschool.net/students/admin/view/${id}";

            //     return view('livewire.link', [
            //         'target' => '_blank',
            //         'href' => $url,
            //         'slot' => $admno
            //         ]);
            // })
            // ->exportCallback(function($id,$admno){
            //     return $admno;
            // })
            // ->unwrap()
            // ->label('Adm. No')
            // ->searchable(),

            Column::make('Adm. No', 'admission_no')
                ->searchable()
                ->sortable(),

            Column::make('Name', 'full_name')
                ->searchable()
                ->sortable(),

            Column::make('Status', 'status')
                ->sortable()
                ->secondaryHeaderFilter('status'),

            Column::make('Gender', 'gender')
                ->sortable()
                ->secondaryHeaderFilter('gender'),

            Column::make('Batch Name', 'full_batch')
                ->sortable()
                ->secondaryHeaderFilter('full_batch'),

            Column::make('Medium', 'medium')
                ->searchable()
                ->sortable(),

            Column::make('First Lang.', 'second_language')
                ->searchable()
                ->sortable()
                ->secondaryHeader(function() {
                    return view('tables.cells.first-lang-list')->with('first_lang_list', $this->getFirstLangCount());
                }),

            Column::make('Digital', 'digital')
                ->searchable()
                ->sortable(),

            Column::make('Contact Phone', 'phone_no')
                ->searchable()
                ->format(fn ($value) => number_format(is_numeric($value) ? $value : 0, 0, '', '')),

            Column::make('Home Phone', 'phone_home')
                ->searchable()
                ->format(fn ($value) => number_format(is_numeric($value) ? $value : 0, 0, '', '')),

            Column::make('Father\'s Phone', 'phone_father')
                ->searchable()
                ->format(fn ($value) => number_format(is_numeric($value) ? $value : 0, 0, '', '')),

            Column::make('Adm. Class', 'admitted_class'),

            Column::make('Adm. Date', 'admission_date')
                ->format(fn ($value) => Carbon::createFromDate($value)->format('d-m-Y'))
                ->sortable(),

            Column::make('Date of Birth', 'dob')
                ->format(fn ($value) => Carbon::createFromDate($value)->format('d-m-Y'))
                ->sortable(),

            Column::make('Name of Father', 'father_name')
                ->searchable(),

            Column::make('Occup. of Father', 'father_occupation')
                ->searchable(),

            /*
            Column::make('father_residential_address')
                ->label('Father Res. Addr')
                ->searchable(),,
            */



            Column::make('Name of Mother', 'mother_name')
                ->searchable(),

            Column::make('Occup. of Mother', 'mother_occupation')
                ->searchable(),

            Column::make('Mother\'s Phone', 'phone_mother')
                ->searchable(),

            /*
                Column::make('mother_residential_address')
                ->label('Mother Res. Addr')
                ->searchable(),
            */

            Column::make('Name of Guardian', 'guardian_name')
                ->searchable(),

            Column::make('Guardian\'s Phone', 'phone_guardian')
                ->searchable(),

            /*
            Column::make('b.guardian_address')
                ->label('Guardian Addr')
                ->searchable(),
            */

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
        // dd($ids);
        $this->clearSelected();
        $sorts = [$this->getDefaultSortColumn() => $this->getDefaultSortDirection()];
        $sorts = $this->getSorts() == [] ? $sorts : $this->getSorts();
        return Excel::download(new StudentsExport($ids, $sorts), 'students.xlsx');
    }


    /*
[
  "class" => array:1 [▶
    0 => "12"
  ]
  "status" => "Active"
]
*/

    public function filters(): array
    {
        $genders = $this->model::query()
            ->distinct()
            ->select('gender')
            ->orderBy('gender')
            ->get()
            ->keyBy('gender')
            ->map(fn ($gender) => $gender->gender)
            ->toArray();

        $all_genders = Arr::prepend($genders, 'All', '');

        if (Arr::has($this->getAppliedFilters(), 'class')) {
            ['class' => $class] = $this->getAppliedFilters();
            if (is_string($class))
                $class = [$class];
        }

        if (empty($class)) {
            $batches = $this->model::query()
                ->distinct()
                ->select('full_batch')
                ->orderBy('class_id')
                ->orderBy('full_batch', 'desc')
                ->get()
                ->keyBy('full_batch')
                ->map(fn ($batch) => $batch->full_batch)
                ->toArray();
        } else {
            $batches = $this->model::query()
                ->distinct()
                ->select('full_batch')
                ->whereIn('class_id', $class)
                ->orderBy('class_id')
                ->orderBy('full_batch', 'desc')
                ->get()
                ->keyBy('full_batch')
                ->map(fn ($batch) => $batch->full_batch)
                ->toArray();
        }
        $all_batches = Arr::prepend($batches, 'All', '');

        $status = $this->model::query()
            ->distinct()
            ->select('status')
            ->orderBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn ($status) => $status->status)
            ->toArray();

        $all_status = Arr::prepend($status, 'All', '');

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

            SelectFilter::make('status')
                ->setFilterPillTitle('status')
                ->setFilterPillValues($status)
                ->options($all_status)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('status', $value);
                })
                ->hiddenFromAll(),

            SelectFilter::make('gender')
                ->setFilterPillTitle('gender')
                ->setFilterPillValues($genders)
                ->options($all_genders)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('gender', $value);
                })
                ->hiddenFromAll(),

        ];
    }
}
