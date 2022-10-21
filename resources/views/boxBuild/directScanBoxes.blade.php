@extends('layouts.app')
@section('title', 'Direct Scan Boxes')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Box-Build list</div>
                    <div class="card-body">
                        <direct-scan-boxes></direct-scan-boxes>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
