@extends('layouts.app')
@section('title', 'Filter')
@section('content')
    <div class="container-big">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('home.dashboard') }}</div>
                    <div id="goodsFlowItemFilter" class="card-body">
                        <all-goodsflow-item-table/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
