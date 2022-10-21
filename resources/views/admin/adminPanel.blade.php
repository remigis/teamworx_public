@extends('layouts.app')
@section('title', 'Admin panel')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('admin.panel') }}</div>
                    <div id="dashboard" class="card-body">
                        @privilege(\App\Utilities\PrivilegeUtilities::PRIVILEGE_TO_REGISTER_NEW_USER)
                        <new-user-registration></new-user-registration>
                        @endprivilege
                        <user-privileges></user-privileges>

                        <gate-phone-number-setter></gate-phone-number-setter>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
