@extends('layouts.app')
@section('title', 'Calculator')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Calculator') }}</div>
                    <div class="card-body">
                        <box-search-input-with-autocomplete class="col-md-6"/>
                    </div>
                    <img style="padding: 30px; height: 300px; width: auto;"
                         src="{{ asset('storage/images/unDraw/undraw_calculator_re_alsc.svg') }}"
                         class="rounded mx-auto d-block" alt="Calculator">
                </div>
            </div>
        </div>
    </div>
@endsection
