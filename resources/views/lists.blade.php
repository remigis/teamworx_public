@extends('layouts.app')
@section('title', 'Lists')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('tools.text_tools') }}</div>
                    <div class="card-body">


                        <div class="container p-4">
                            <h2 class="pb-2 border-bottom my-3"><i class="fa-solid fa-file"></i> Data lists</h2>
                            <div class="row">
                                <div class="card my-2 col-12">
                                    <div class="card-body">
                                        <h4>GF Items</h4>
                                        <p>Paragraph of text beneath the heading to
                                            explain
                                            the heading. We'll add onto it with another sentence and probably just
                                            keep
                                            going until we run out of words.</p>
                                        <a href="{{route('goodsFlowItemFilterView')}}"
                                           class="col-md-6 btn btn-primary float-right">
                                            <i class="fa-solid fa-person-booth"></i> Enter
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <h2 class="pb-2 border-bottom my-3"><i class="fa-solid fa-file-pen"></i> Active lists</h2>
                            <div class="row">
                                <div class="card my-2 col-12">
                                    <div class="card-body">
                                        <h4>(RAZER) Battery/Scrap</h4>
                                        <p>
                                            List of RAZER items with battery and direct scrap info.
                                        </p>
                                        <a href="{{route('RAZERBatteryGoodBad')}}"
                                           class="col-md-6 btn btn-primary float-right">
                                            <i class="fa-solid fa-person-booth"></i> Enter
                                        </a>
                                    </div>
                                </div>
                                <div class="card my-2 col-12">
                                    <div class="card-body">
                                        <h4>Required List</h4>
                                        <p>
                                            List of items that needs to be tested as soon as possible
                                        </p>
                                        <a href="{{route('requiredList')}}"
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
@endsection
