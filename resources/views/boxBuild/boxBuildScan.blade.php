@extends('layouts.app')
@section('title', 'Box-Build Scan')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Box build scanner</div>
                    <div class="card-body">
                        <box-build-scanner user-id="{{Auth::user()->id}}"></box-build-scanner>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
