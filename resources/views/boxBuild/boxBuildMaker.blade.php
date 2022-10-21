@extends('layouts.app')
@section('title', 'Box-Build Maker')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Create box-build ') }}</div>
                    <div class="card-body">
                        <box-build-creator></box-build-creator>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
