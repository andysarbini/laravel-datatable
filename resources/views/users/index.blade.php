@extends('layouts.app')

@section('content')

    <style>
        @import "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css";
        /* @import "//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"; */
    </style>
    
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