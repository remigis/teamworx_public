@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Register') }}</div>

                    <registration-with-invitation name="{{$name}}" email="{{$email}}"
                                                  token="{{$token}}"></registration-with-invitation>

                </div>
            </div>
        </div>
    </div>
@endsection
