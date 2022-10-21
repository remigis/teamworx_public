@extends('layouts.app')
@section('title', 'Label: '.$text)
@section('content')
    <div class="container bg-dark py-3">

        <div class="label text-center p-3 my-2 bg-white">
            <div class="justify-content-center bg-white">
                <div class="col-xs-12">
                    <H1 class="h1 font-weight-bolder box-name"> {{$text}} </H1>
                </div>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-6 count">
                            {{$number}}
                        </div>
                        <div class="col-6 width50 padtop10">
                            <qr-code text="{{$key}}"></qr-code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
