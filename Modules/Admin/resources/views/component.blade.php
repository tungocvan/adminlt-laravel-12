@extends('adminlte::page')

@section('plugins.TempusDominusBs4', true)
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.Summernote', true)

@section('title', 'Dashboard')


@section('content_header')
    <h1>Dashboard</h1>
@stop

@php
    $configDate = [
        'format' => 'DD-MM-YYYY',
    ];
    $dateNow = date('d-m-Y'); // https://niithanoi.edu.vn/cach-su-dung-date-va-time-trong-php.html


    $heads = [
    'ID',
    'Name',
    ['label' => 'Phone', 'width' => 40],
    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
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

$config = [
    'data' => [
        [22, 'John Bender', '+02 (123) 123456789', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
        [19, 'Sophia Clemens', '+99 (987) 987654321', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
        [3, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.$btnEdit.$btnDelete.$btnDetails.'</nobr>'],
    ],
    'order' => [[1, 'asc']],
    'columns' => [null, null, null, ['orderable' => false]],
];

$configSelect2 = [
        "placeholder" => "Select multiple options...",
        "allowClear" => true,
];

$configTextEditor = [
    "height" => "100",
    "toolbar" => [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']],
    ],
]

@endphp

@section('content')
    <p>Test Component AdminLTE 3.</p>
    <x-adminlte-alert>
    <div class="row">
        <div class="col-3">
            <x-adminlte-info-box title="528" text="User Registrations" icon="fas fa-lg fa-user-plus text-primary"
        theme="gradient-primary" icon-theme="white"/>
        </div>
        <div class="col-3">
            <x-adminlte-info-box title="528" text="User Registrations" icon="fas fa-lg fa-user-plus text-primary"
        theme="gradient-primary" icon-theme="white"/>
        </div>
    </div>
    </x-adminlte-alert>
    <x-adminlte-alert>
    <div class="row">
        <div class="col-12">
            <h3>FORM BASIC</h3>   
            <x-adminlte-input-date name="idDisabled" value="{{$dateNow}}"  :config="$configDate" />
        </div>
    </div>
    </x-adminlte-alert>
    <x-adminlte-alert>
    <div class="row">
        <div class="col-12">
            <h3>DATATABLES</h3>   
            <x-adminlte-datatable id="table1" :heads="$heads">
                @foreach($config['data'] as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{!! $cell !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <x-adminlte-text-editor name="teConfig" label="WYSIWYG Editor" label-class="text-danger"
    igroup-size="sm" placeholder="Write some text..." :config="$configTextEditor"/>
        </div>
        <div class="col-4">
            <x-adminlte-select2 id="sel2Category" name="sel2Category[]" label="Categories"
            label-class="text-danger" igroup-size="sm" :config="$configSelect2" multiple>
            <x-slot name="prependSlot">
                <div class="input-group-text bg-gradient-red">
                    <i class="fas fa-tag"></i>
                </div>
            </x-slot>
            <x-slot name="appendSlot">
                <x-adminlte-button theme="outline-dark" label="Clear" icon="fas fa-lg fa-ban text-danger"/>
            </x-slot>
            <option>Sports</option>
            <option>News</option>
            <option>Games</option>
            <option>Science</option>
            <option>Maths</option>
        </x-adminlte-select2>            
        </div>
        
    </div>
    </x-adminlte-alert>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>   
@stop
