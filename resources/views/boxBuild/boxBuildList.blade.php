@extends('layouts.app')
@section('title', 'Box-Build List')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Box-Build list</div>
                    <div class="card-body">
                        <box-build-list></box-build-list>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
