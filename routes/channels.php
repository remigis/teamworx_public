<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('ItemScan', function () {
    return true;
});
Broadcast::channel('CreateScan', function () {
    return true;
});
Broadcast::channel('CloseScan', function () {
    return true;
});
Broadcast::channel('NewScanFileUpload', function () {
    return true;
});
Broadcast::channel('NewItemScanListChange', function () {
    return true;
});
Broadcast::channel('RazerAPIStatusChange', function () {
    return true;
});
Broadcast::channel('CheckRequiredListStatusChange', function () {
    return true;
});
Broadcast::channel('NewRequiredListCreatedEvent', function () {
    return true;
});
Broadcast::channel('BoxBuildDirectScanCenterChangeEvent', function () {
    return true;
});
Broadcast::channel('BoxBuildDirectScanStatusChangeEvent', function () {
    return true;
});
Broadcast::channel('BoxBuildItemChangeEvent', function () {
    return true;
});
Broadcast::channel('GatesCallbackEvent', function () {
    return true;
});
