<?php

namespace App\DataTables;

use App\Models\Company;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Spatie\Permission\Models\Role;

class CompanyDataTable extends DataTable
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
            ->setRowClass(function ($company) {
                return '';
            })
            ->addColumn('checkbox', static function (Company $company) {
                return "<input type='checkbox' data-id='$company->id' class='select-checkbox' name='id[]'>";
            })
            /*->addColumn('companies', static function (Company $company) {
                $companies = Company::where('user_id', $company->id)->pluck('name')->toArray();
                if (is_array($companies)) {
                    return implode(', ', $companies);
                }
            })*/
            ->addColumn('action', static function (Company $company) {
                return view('backend.pages.companies.action', compact('company'));
            })
            ->setRowId(function ($company) {
                return 'user-'.$company->id;
            })
            ->rawColumns(['checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Company $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Company $company)
    {
        return $company->orderBy('id', 'DESC')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('companies-table')
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
            Column::make('name'),        
            Column::make('commercial_name'),
            Column::make('email'),
            Column::make('action')->className('action-column'),        
        ];
    }
}