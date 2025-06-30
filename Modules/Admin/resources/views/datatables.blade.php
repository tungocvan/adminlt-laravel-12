@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)


@section('title', 'Dashboard')


@section('content_header')
    <h1>Dashboard</h1>
@stop

@php
    $heads = [
    ['label' => '<input type="checkbox" onclick="toggleSelectAll(this)">', 'no-export' => true, 'width' => 5],
    ['data' => 'id', 'label' => 'ID'],
    ['data' => 'name', 'label' => 'Name'],
    ['label' => 'Phone', 'width' => 40],
    [
            'label' => 'Actions',
            'no-export' => true,
            'width' => 5,
            'attributes' => isset($th['no-export']) ? 'dt-no-export' : '', // Thêm điều kiện cho dt-no-export
    ],
];

$btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                <i class="fa fa-lg fa-fw fa-pen"></i>
            </button>';
$btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
              </button>';
$btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                   <i class="fa fa-lg fa-fw fa-eye"></i>
               </button>';


// Button to change the page length of tables.

$lengthBtn = [
    'extend' => 'pageLength',
    'className' => 'btn-default',
];

// Button to print the data.

$printBtn = [
    'extend' => 'print',
    'className' => 'btn-default',
    'text' => '<i class="fas fa-fw fa-lg fa-print"></i>',
    'titleAttr' => 'Print',
    'exportOptions' => ['columns' => ':not([dt-no-export])'],
];

$excelBtn = [
    'extend' => 'excel',
    'className' => 'btn-default',
    'text' => '<i class="fas fa-fw fa-lg fa-file-excel text-success"></i>',
    'titleAttr' => 'Export to Excel',
    'exportOptions' => ['columns' => ':not([dt-no-export])'],
];

$pdfBtn = [
            'extend' => 'pdf',
            'className' => 'btn-default',
            'text' => '<i class="fas fa-fw fa-lg fa-file-pdf text-danger"></i>',
            'titleAttr' => 'Export to PDF',
            'exportOptions' => ['columns' => ':not([dt-no-export])'],
        ];



$config = [
    'data' => [
            ['<input type="checkbox" class="select-row" name="chk-1">', 22, 'John Bender', '+02 (123) 123456789', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
            ['<input type="checkbox" class="select-row" name="chk-2">', 19, 'Sophia Clemens', '+99 (987) 987654321', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
            ['<input type="checkbox" class="select-row" name="chk-3">', 3, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
    ],
    'order' => [[1, 'asc']],
    'columns' => [['orderable' => false], null, null, null, ['orderable' => false]],
    'lengthMenu' =>[[10, 25, 50, -1],['10 rows', '25 rows', '50 rows', 'Show all']],
    'buttons' => [$lengthBtn,$excelBtn,$printBtn,$pdfBtn]
];

@endphp

@section('content')
    <p>DATATABLES AdminLTE 3.</p>
    <x-adminlte-datatable id="table7" :heads="$heads" head-theme="light" theme="warning" :config="$config"
    striped hoverable with-buttons/>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
@stop

@section('js')
<script>
   function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('#table7 tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
         }
</script>
@stop
