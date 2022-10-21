@extends('layouts.app')
@section('title', 'Scan: '.$box->name)
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Scan') }} {{$box->name}} - {{$box->id}}</div>
                    <div class="card-body">
                        <box-scan box-id="{{$box->id}}"></box-scan>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
