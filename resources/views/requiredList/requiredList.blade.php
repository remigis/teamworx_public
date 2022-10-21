@extends('layouts.app')
@section('title', 'Required List Maker')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('tools.required_list') }}</div>
                    <div class="card-body">
                        <required-list-maker></required-list-maker>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
