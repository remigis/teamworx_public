@extends('layouts.app')
@section('title', 'Label: '.$box->name)
@section('content')
    <div class="container container bg- hide-on-print py-3">
        <div class="row">
            <print-button></print-button>
        </div>
    </div>
    <div class="container bg-dark py-3">
        @for ($i = 1; $i <= $count; $i++)

            <div class="label text-center p-3 my-2 bg-white @if($i != $count) new-page @endif">
                <div class="justify-content-center bg-white">
                    <div class="col-xs-12">
                        <H1 class="h1 font-weight-bolder box-name"> {{$box->name}} </H1>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-6 count">
                                {{$i}}/{{request()->route('count')}}
                            </div>
                            <div class="col-6 width50 padtop10">
                                <box-label-barcodes box-id="{{$box->id}}"></box-label-barcodes>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endfor
    </div>
@endsection
