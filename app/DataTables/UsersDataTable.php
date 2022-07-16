<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;

// `use Yajra\DataTables\DataTables`;

class UsersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */

    protected $actions = ['print', 'export', 'csv', 'excel', 'hapusUser'];

    public function dataTable($query, Request $request)
    {
        return dataTables()
            ->eloquent($query)
            ->addColumn('more', '<i class="fa fa-plus"> </i>')
            // ->addColumn('info_detail', 'Detail dari {{$name}}')
            ->addColumn('info_detail', function(User $user) {
                return view('users.info-detail', compact('user'));
            })
            ->addColumn('sapa_nama', 'Halo {{$name}}')
            ->addColumn('post_url', function(User $user) {
                return url("/users/$user->id/posts");
            })
            ->addColumn('post_detail', function(User $user) {
                return view('users.posts-detail', ['user' => $user]);
            })
            // ->addColumn('action', '
            //     <a href="/users/{{$id}}/edit" class="btn btn-primary btn-sm">Edit</a>
            //     <form method="POST" class="d-inline" action="/users/{{$id}}">
            //         @method("DELETE")
            //         @csrf
            //         <input type="hidden" value="{{$id}}" />
                    
            //         <button class="btn btn-danger btn-sm"> Hapus </button>
            //     </form>
            //     ')
            ->addColumn('action', function(User $user) {
                return view('users.actions', compact('user'));
            })
            ->addColumn('posts', function(User $user) {
                return $user->posts->map(function($post) {
                    return \Str::limit($post->title, 4, '...'); // 4 karakter
                })->implode('<br>');
            })
            ->setRowClass(function ($user) {
                if($user->name == "Spencer Mayer") return 'alert-success';
            })
            ->editColumn('created_at', function(User $user){
                return $user->created_at->format('d/m/Y');
            })
            ->editColumn('updated_at', function(User $user){
                return $user->updated_at->format('d/m/Y');
            })
            ->rawColumns(['more', 'action', 'posts'])
            ->filter(function($query) use($request) {
                // if($request->has('email')) {
                //     $email = $request->get("email");
                //     return $query->where('email', 'LIKE', "%$email%");
                // }
                if($request->has('operator') && $request->has('jumlah_post')) {
                    $operator = $request->get('operator');
                    $jumlah = $request->get('jumlah_post');
                    return $query->withCount('posts')->having('posts_count', $operator, $jumlah);
                }

                if($request->has('email')) {
                    $email = $request->get('email');
                    $query->where('email', 'LIKE', "%$email%");
                }
                return $query;

            }, true);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        // return $model->newQuery();
        return $model->with('posts')->select('users.*')->newQuery(); // eloquent relationship
        // return $model
        // ->join('posts', 'users.id', '=', 'posts.author_id')
        // ->select(['users.id', 'users.name', 'users.email', 'posts.title', 'users.created_at', 'users.updated_at']); // query builder
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
                    ->dom('Brtip') 
                    // kepanjangan Bfrtip utk setting tampilan datatable:
                        // Button, extension
                        // Filter, pencarian global
                        // Table, dari datatable itu sendiri
                        // Information, ringkasan table
                        // Pagination, tombol paginasi
                        // Length, alias opsi pilihan jumlah data yang ditampilkan per halaman
                    ->orderBy(2, 'desc')                   
                    ->parameters([
                        'initComplete' => $this->initComplete(),
                        'drawCallback' => $this->drawCallback()
                         
                    ])
                    ->addCheckbox(["class" => "selection", "title" => ""], true)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make(["text" => "Hapus"])->action($this->hapusActionCallback()),
                        // Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            // Column::computed('more')->addClass('details-control'),
            // Column::computed('action')
            //       ->exportable(false)
            //       ->printable(false)
            //       ->width(60)
            //       ->addClass('text-center'),
            // Column::make('id'),
            // // Column::make('email')sortable(false)->searchable(false), tidak bisa dicari & sortir
            // Column::make('email')->searchable(false),
            // Column::computed('email')->title('Surel'),  /** <-- tidak bisa dicari & sortir */
            // // Column::make('name')->sortable(false), tidak bisa disortir
            // Column::make('name')->title('Nama Lengkap'),
            // Column::make('sapa_nama'),
            Column::computed('action')
                ->width(160)
                ->addClass('text-center'),
            Column::make('id', 'users.id'),
            Column::make('email', 'users.email')->title('Email')->printable(false),
            Column::make('name', 'users.name')->title('Nama Lengkap')->exportable(false),
            Column::make('posts', 'posts.title'),
            Column::make('created_at'),
            Column::make('updated_at')
        ];
        //     return [
        //         'id',
        //         'email',
        //         'name',
        //         'created_at',
        //         'updated_at'
        //     ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Users_' . date('YmdHis');
    }

    public function initComplete() {
        return 'function() {
            let data = this.api().data();
            window.selected = [];
            
            $("#users-table tbody").on("click", "input.selection", function() {
                let tr = $(this).closest("tr")[0];
                let row = data[tr.sectionRowIndex];
                let checked = $(this).is(":checked");
                
                if(checked) return selected.push(row.id);
                selected.filter(id => id !== row.id)
            })

            function format(d){ return d.info_detail }
                // 1. Dapatkan instance datatable di javascript
                let table = this.api();

                $("#users-table").on("click", "td.details-control", function(){
                // 2. dapatkan elemen `tr` yang mewakili baris dari ikon yang diklik
                    let tr = $(this).closest("tr");

                    // 3. dapatkan baris di datatable berdasarkan `id` di atas
                    let row = table.row(tr);
                    let tableId = "posts-" + row.data().id;

                    // 4. check apakah baris sedang terlihat (visible)
                    if (row.child.isShown() ) {
                        // jika posisi sekarang terlihat, maka hide
                        row.child.hide();
                        tr.removeClass("shown");
                    }
                    else {
                        // jika posisi sekarang tidak terlihat (hidden), maka perlihatkan
                        // dengan data "Hello again"
                        // row.child(row.data().info_detail).show();
                        row.child(row.data().post_detail).show();
                        initTable(tableId, row.data().post_url)
                        tr.addClass("shown");
                    }
            })

            function initTable(tableId, posts_detail_url) {
                $("#" + tableId).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: posts_detail_url,
                    dom: "Bfrtip",
                    columns: [
                        { data: "id", name: "id" },
                        { data: "title", name: "title" },
                        ]
                    })
                }

        }
      ';
    }

    public function hapusActionCallback() {
        return 'function(e, dt, node, config) {
            // alert(JSON.stringify(window.selected));
            let _buildUrl = function(dt, action) {
                let url = dt.ajax.url() || "";
                let params = dt.ajax.params();
                params.action = action;

                if (url.indexOf("?") > -1) {
                    return url + "&" + $.param(params);
                }

                return url + "?" + $.param(params);
            };

            let url = _buildUrl(dt, "hapusUsers");
            window.location = url + "&selected=" + window.selected;

        }';
    }

    public function hapusUsers() {
        $selectedIds = $this->request()->get('selected');
        return User::whereIn('id', explode(',', $selectedIds))->get();

        User::whereIn('id', $selectedIds)->delete();
        return redirect()->back();
    }

    public function drawCallback() {
        return 'function() {
            let data = this.api().data();
            let selected = window.selected || [];
            
            $("input.selection").each(function() {
                let tr = $(this).closest("tr")[0];
                let row = data[tr.sectionRowIndex];
                
                if(selected.indexOf(row.id) > -1) {
                    $(this).attr("checked", true);
                }
            })
        }';
    }
    
    // public function aktifkanUsers() { // untuk aktifkan status user
    //     $selectedIds = $this->request()->get('selected');
    //     return User::whereIn('id', explode(',', $selectedIds))->get();

    //     User::whereIn('id', $selectedIds)->update(["status" => "aktif"]);
    //     return redirect()->back();
    // }



}
