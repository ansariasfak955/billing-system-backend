<?php

namespace App\DataTables;

use App\Models\ActivityType;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Spatie\Permission\Models\Role;

class ActivityTypeDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)->addIndexColumn()
            ->setRowClass(function ($activity_type) {
                return '';
            })
            ->addColumn('action', static function (ActivityType $activity_type) {
                return view('backend.pages.activity-type.action', compact('activity_type'));
            })
            ->setRowId(function ($activity_type) {
                return 'activity_type-'.$activity_type->id;
            })
            ->rawColumns(['checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ActivityType $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ActivityType $activity_type)
    {
        return $activity_type->orderBy('id', 'DESC')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('activity_types-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addTableClass(['table-hover', 'table-bordered', 'table-sm'])
            ->parameters([
                'lengthMenu' => [
                    [ 25, 50, 100, 500],
                    [ '25', '50', '100', '500']
                ]
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        // $checkbox = '<input class="select-all" type="checkbox" name="select_all" value="1" id="select">';
        return [
            Column::make('DT_RowIndex')->title('Sr. no.')->searchable(false)->orderable(false),
            Column::make('activity_type'),
            Column::make('action')->className('action-column'),
        ];
    }
}