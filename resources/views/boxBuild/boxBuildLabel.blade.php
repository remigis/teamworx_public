@extends('layouts.app')
@section('title', 'Label: '.$boxName)
@section('content')
    <div class="container bg- hide-on-print py-3">
        <button class="btn btn-primary hide-on-print btn-sm"
                data-clipboard-text="{{$xlsxText}}">
            <i class="fa-solid fa-copy"></i> Copy XLSX line
        </button>
        <print-button></print-button>
    </div>
    <div class="container bg-dark py-3">

        <div class="label text-center p-3 my-2 bg-white new-page">
            <div class="justify-content-center bg-white">
                <div class="col-xs-12">
                    <H1 class="h1 font-weight-bolder box-name"> {{$boxName}} </H1>
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-4">
                            <div class="col-12 count">
                                {{$vid}}
                            </div>
                            <div class="col-12 count">
                                Items: {{$itemCount}}
                            </div>
                        </div>

                        <div class="col-8 width50 padtop10">
                            <barcode text="{{$boxName}}"></barcode>
                            <barcode text="{{$boxId}}"></barcode>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
