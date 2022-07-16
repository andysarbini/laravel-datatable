@extends('layouts.app')

@section('content')

    <style>
        @import "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css";
        /* @import "//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"; */
    </style>
    
    <div class="container">
        <div class="row">
            <form action="/users" method="get" class="form-inline">
                {{-- <input type="text" class="form-control" name="email" value="{{\Request::get('email')}}"> --}}

                <div class="form-group mr-2">
                    <div class="mr-2"> Email </div>
                    <input type="text" class="form-control" name="email" value="{{\Request::get('email')}}">
                </div>

                <div class="form-group mr-2">
                    <div class="mr-2">Jumlah Post</div>
                    <select name="operator" id="" class="form-control">
                        <option value=">="> &gt;= </option>
                        <option value=">"> &gt; </option>
                        <option value="<"> &lt; </option>
                        <option value="="> = </option>
                    </select>
                    <input type="text" class="form-control" name="jumlah_post" value="{{\Request::get('jumlah_post')}}">
                </div>

                <button type="submit" class="btn btn-primary"> Cari </button>
            </form>
        </div>
    </div>

    <hr>

    <div class="container-fluid">
        {{-- {{ $dataTable->table() }} --}}
        {{-- {!! $dataTable->table() !!}
        {!! $dataTable->scripts() !!} --}}

        {{-- berasal dr plugin htmlbuilder --}}
        {{ $dataTable->table([], true) }} {{-- [] -> atribut yang ingin diberikan ke table, true -> untuk merender footer --}}
        {{-- {{ $dataTable->scripts() }} --}}
    </div>
    
    
    
@endsection
    
@push('scripts')

{{-- berasal dr plugin htmlbuilder --}}
    {{ $dataTable->scripts() }}
    {{-- {!! $dataTable->scripts() !!} --}}
@endpush

@push('styles')
    <style>
        .dataTables_filter{
            display: none
        }
    </style>
@endpush