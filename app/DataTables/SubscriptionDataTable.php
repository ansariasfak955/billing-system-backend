<?php

namespace App\DataTables;

use App\Models\Subscription;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Spatie\Permission\Models\Role;

class SubscriptionDataTable extends DataTable
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
            ->setRowClass(function ($subscription) {
                return '';
            })
            ->addColumn('action', static function (Subscription $subscription) {
                return view('backend.pages.subscriptions.action', compact('subscription'));
            })
            ->setRowId(function ($subscription) {
                return 'subscription-'.$subscription->id;
            })
            ->rawColumns(['checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Subscription $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Subscription $subscription)
    {
        return $subscription->orderBy('id', 'DESC')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('subscriptions-table')
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
            Column::make('name'),
            Column::make('price'),
            Column::make('description'),
            Column::make('action')->className('action-column'),
        ];
    }
}