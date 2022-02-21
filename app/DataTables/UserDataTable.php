<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Spatie\Permission\Models\Role;

class UserDataTable extends DataTable
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
            ->setRowClass(function ($user) {
                    return '';
            })
            ->addColumn('checkbox', static function (User $user) {
                return "<input type='checkbox' data-id='$user->id' class='select-checkbox' name='id[]'>";
            })
           
            
            ->setRowId(function ($user) {
                return 'user-'.$user->id;
            })
            ->rawColumns(['checkbox','image', 'role']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $user)
    {
        dd("jsghdfj");
        return $user->orderBy('id', 'DESC')->newQuery();
        
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('users-table')
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
        $checkbox = '<input class="select-all" type="checkbox" name="select_all" value="1" id="select">';
        return [
            Column::make('checkbox')->title($checkbox)->className('all_items_checkbox')->searchable(false)->orderable(false),
            Column::make('DT_RowIndex')->title('Sr. no.')->searchable(false)->orderable(false),
            //Column::make('role'),        
            Column::make('name'),        
            Column::make('email'),    
            Column::make('status'),    
            Column::make('action')->className('action-column'),        
        ];
    }
}
