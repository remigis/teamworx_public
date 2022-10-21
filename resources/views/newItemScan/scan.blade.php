@extends('layouts.app')
@section('title', 'New item Scan: '.$scan->name)
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{$scan->name}}</div>
                    <div class="card-body">
                        <new-item-scanner scan-id="{{$scan->id}}" user-id="{{Auth::user()->id}}"></new-item-scanner>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
