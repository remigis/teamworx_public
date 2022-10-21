@extends('layouts.app')
@section('title', 'Battery Good/Bad EAN')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('RAZER Bateries - Good/Bad') }}</div>
                    <div class="card-body">
                        <razer-battery-good-bad></razer-battery-good-bad>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
