@extends('adminlte::page')

@section('title', 'Dashboard')

@php
    $config = [
        'title' => 'Select multiple options...',
        'liveSearch' => true,
        'liveSearchPlaceholder' => 'Search...',
        'showTick' => true,
        'actionsBox' => true,
    ];
@endphp

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
    <x-adminlte-alert>

        <div class="row">
            <div class="col-3">
                <x-adminlte-info-box title="528" text="User Registrations" icon="fas fa-lg fa-user-plus text-primary"
                    theme="gradient-primary" icon-theme="white" />
            </div>
            <div class="col-3">
                <x-adminlte-info-box title="528" text="User Registrations" icon="fas fa-lg fa-user-plus text-primary"
                    theme="gradient-primary" icon-theme="white" />
            </div>
            <div class="col-3">
                <input type="text" name="birthday" id="endDate" value="24/10/1984" />
                <x-adminlte-select2 name="sel2Basic">
                    <option>Option 1</option>
                    <option disabled>Option 2</option>
                    <option selected>Option 3</option>
                </x-adminlte-select2>
                <x-adminlte-select2 name="sel2Vehicle" label="Vehicle" label-class="text-lightblue" igroup-size="lg"
                    data-placeholder="Select an option...">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="fas fa-car-side"></i>
                        </div>
                    </x-slot>
                    <option>Vehicle 1</option>
                    <option>Vehicle 2</option>
                </x-adminlte-select2>

                <x-adminlte-select-bs id="optionsCategory" name="optionsCategory[]" label="Categories"
                    label-class="text-danger" :config="$config" multiple>
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-red">
                            <i class="fas fa-tag"></i>
                        </div>
                    </x-slot>
                    <x-adminlte-options :options="['News', 'Sports', 'Science', 'Games']" />
                </x-adminlte-select-bs>
            </div>
        </div>
    </x-adminlte-alert>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@stop

@section('js')
    {{-- https://www.daterangepicker.com/#examples  --}}
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            $('input[name="birthday"]').daterangepicker({
                endDate: moment().format(),
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        });
    </script>

@stop
