@extends('layouts.app')
@section('title', 'Box-Build Menu')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('tools.text_tools') }}</div>
                    <div class="card-body">
                        <div class="container p-4">
                            <div class="row">
                                <div class="my-2 p-3 col-md-12">
                                    <div class="card col-md-12">
                                        <div class="card-body">
                                            <h4><i class="fa-solid fa-barcode"></i> Box-Build scan</h4>
                                            <p>Scan items to active box build</p>
                                            <a href="{{route('boxBuildScan')}}"
                                               class="col-md-6 btn btn-primary float-right">
                                                <i class="fa-solid fa-person-booth"></i> Enter
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-2 p-3 col-md-6">
                                    <div class="card col-md-12">
                                        <div class="card-body">
                                            <h4><i class="fa-solid fa-list"></i> Box-Build List</h4>
                                            <p>Here is the list of all created box builds.</p>
                                            <a href="{{route('boxBuildList')}}"
                                               class="col-md-6 btn btn-primary float-right">
                                                <i class="fa-solid fa-person-booth"></i> Enter
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-2 p-3 col-md-6">
                                    <div class="card col-md-12">
                                        <div class="card-body">
                                            <h4><i class="fa-solid fa-plus"></i> Create Box-Build</h4>
                                            <p>Here you can create new box builds.</p>
                                            <a href="{{route('boxBuildMaker')}}"
                                               class="col-md-6 btn btn-primary float-right">
                                                <i class="fa-solid fa-person-booth"></i> Enter
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-2 p-3 col-md-6">
                                    <div class="card col-md-12">
                                        <div class="card-body">
                                            <h4><i class="fa-solid fa-square-up-right"></i> Direct scan boxes</h4>
                                            <p>Here you can find all the boxes that were created with direct scan.</p>
                                            <a href="{{route('directScanBoxes')}}"
                                               class="col-md-6 btn btn-primary float-right">
                                                <i class="fa-solid fa-person-booth"></i> Enter
                                            </a>
                                        </div>
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
