<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\DataTables\UsersDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


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

    // public function index()
    // {
    //     $list_user = User::all();
    //     return response()->json();
    // }
}
