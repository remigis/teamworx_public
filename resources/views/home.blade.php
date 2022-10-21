@extends('layouts.app')
@section('title', 'Settings')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('home.settings') }}</div>
                    <div id="dashboard" class="card-body">
                        @privilege([\App\Utilities\PrivilegeUtilities::PRIVILEGE_TO_EDIT_ASSISTANT_VOICE])
                        <voice-settings user-id="{{Auth::user()->id}}"></voice-settings>
                        @endprivilege
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
