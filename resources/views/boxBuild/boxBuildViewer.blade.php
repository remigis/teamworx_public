@extends('layouts.app')
@section('title', $name)
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ $name }}</div>
                    <div class="card-body">
                        <box-build-viewer box-build-id="{{$id}}"></box-build-viewer>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
