@extends('layouts.app')
@section('title', 'Calculator: '.$box['name'])
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><b>{{$box['name']}}</b>
                        <button data-toggle="tooltip" title="Copy box name" class="btn btn-outline-primary btn-sm"
                                data-clipboard-text="{{$box['name']}}">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                        (Sphere: <span style="font-weight: bold;">{{$box['sphere']['name']}}</span> Box ID: <span
                            style="font-weight: bold;">{{$box['id']}}</span>)
                    </div>
                    <div id="dashboard" class="card-body">
                        <div class="row">
                            <box-search-input-with-autocomplete
                                class="col-md-6 col-sm-12 p-1"></box-search-input-with-autocomplete>
                            <box-link-button class="col-md-2 col-sm-6 p-1"
                                             box-id="{{$box['id']}}"
                                             domain="{{env('GF_DOMAIN')}}"></box-link-button>
                            <box-action-button class="col-md-4 col-sm-6 p-1"
                                               delivery-string="{{$deliveryString}}"
                                               box-id="{{$box['id']}}"
                            ></box-action-button>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="font-weight-bold text-success text-uppercase mb-1">
                                                    Good items: {{$goodItems}}
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <span
                                                        class="badge badge-light badge-pill">A - {{$conditionACount}}</span>
                                                    <span
                                                        class="badge badge-light badge-pill">B - {{$conditionBCount}}</span>
                                                    <span
                                                        class="badge badge-light badge-pill">C - {{$conditionCCount}}</span>
                                                    <span
                                                        class="badge badge-light badge-pill">D - {{$conditionDCount}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="font-weight-bold text-info text-uppercase mb-1">
                                                    All items: {{$allItems}}
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <span
                                                        class="badge badge-light badge-pill">U - {{$conditionUCount}}</span>
                                                    <span
                                                        class="badge badge-light badge-pill">S - {{$conditionSCount}}</span>
                                                    <span
                                                        class="badge badge-light badge-pill">R - {{$conditionRCount}}</span>
                                                    <span
                                                        class="badge badge-light badge-pill">X - {{$conditionXCount}}</span>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tasks Card Example -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Progress
                                                </div>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <div
                                                            class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{$compleationProcentage}}
                                                            %
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="progress progress-sm mr-2">
                                                            <div class="progress-bar bg-info" role="progressbar"
                                                                 style="width: {{$compleationProcentage}}%"
                                                                 aria-valuenow="{{$compleationProcentage}}"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Requests Card Example -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <sn-checker box-id="{{$box['id']}}"></sn-checker>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">


                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <box-item-condition-chart condition-a-count="{{$conditionACount}}"
                                                                          condition-b-count="{{$conditionBCount}}"
                                                                          condition-c-count="{{$conditionCCount}}"
                                                                          condition-d-count="{{$conditionDCount}}"
                                                                          condition-r-count="{{$conditionRCount}}"
                                                                          condition-s-count="{{$conditionSCount}}"
                                                                          condition-u-count="{{$conditionUCount}}"
                                                ></box-item-condition-chart>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-8 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100">
                                    <div
                                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">{{$box['palette']['name']??"Palette name not found"}}</h6>
                                        {{$box['palette']['sphere']['name']??"Palette sphere not found"}},
                                        Boxes: {{$box['palette']['kartonsCount']}}
                                    </div>
                                    <div class="card-body overflow-y-scroll" style="height: 300px;">
                                        @if(!empty($box['palette']['kartons']))
                                            @foreach($box['palette']['kartons'] as $carton)
                                                <a class="text-decoration-none"
                                                   href="{{route('box', ["id" => $carton['id']])}}">
                                                    <div
                                                        class="btn-outline-primary zoom-small col-12 card card-body m-1">
                                                        <div class="row">
                                                            <div class="col-2 p-1">{{$carton['id']}}</div>
                                                            <div
                                                                class="col-9 p-1 ellipsis-left">{{$carton['name']}}</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        @else
                                            <img style="padding: 30px; height: 100%; width: auto;"
                                                 src="{{ asset('storage/images/unDraw/undraw_faq_re_31cw.svg') }}"
                                                 class="rounded mx-auto d-block" alt="WTF ?">
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <box-goodsflow-item-table box-id="{{$box['id']}}"
                                                  class="vuetables my-4"></box-goodsflow-item-table>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
