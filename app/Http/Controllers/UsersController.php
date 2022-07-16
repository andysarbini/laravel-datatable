<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\DataTables\UsersDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Column;


class UsersController extends Controller
{
    //
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
        // dd($dataTable->ajax());
        // dd($dataTable->render('users.index'));
    }

    public function posts($id) {
        $model = Post::where('author_id', $id)->with('author');
        return Datatables::of($model)->toJson();
    }

    public function baru(UsersDataTable $dataTable) {
        return $dataTable
            ->before(function($dataTable) {
                return $dataTable
                    ->addColumn('test', 'Kolom Extended {{$name}}');
            })
            ->withHtml(function($builder) {

                return $builder
                    ->dom("B")
                    ->columns([
                        Column::computed('action')
                            ->width(160)
                            ->addClass('text-center'),
                        Column::make('id', 'users.id'),
                        Column::make('email', 'users.email')->title('Email')->printable(false),
                        Column::make('name', 'users.name')->title('Nama Lengkap')->exportable(false),
                        Column::make('posts', 'posts.title'),
                        Column::make('test')
                    ])
                    ->addCheckbox(["class" => "selection", "title" => ""], true);
            })
        ->render('users.index');
    }

    public function dataTable($query, Request $request) {
        
    }

    // public function index()
    // {
    //     $list_user = User::all();
    //     return response()->json();
    // }
}
