@extends('layouts.app')
@section('title', 'Create New Item Scan')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('New Item Scan') }}</div>
                    <div class="card-body">
                        <new-item-scan auth-user-id="{{Auth::user()->id}}"></new-item-scan>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
