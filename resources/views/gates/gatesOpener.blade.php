@extends('layouts.app')
@section('title', 'Gates')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Gates') }}</div>
                    <div class="card-body">

                        <div class="container p-4">
                            <div class="row">
                                <div class="card my-2 col-12">
                                    <div class="card-body">
                                        <gate-opener></gate-opener>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
