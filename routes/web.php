<?php

use App\Events\GatesCallbackEvent;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\BoxBuildController;
use App\Http\Controllers\BoxBuildEditController;
use App\Http\Controllers\BoxBuildMakerController;
use App\Http\Controllers\BoxBuildScanController;
use App\Http\Controllers\BoxBuildViewerController;
use App\Http\Controllers\BoxScanController;
use App\Http\Controllers\Calculator;
use App\Http\Controllers\ContactUsFormController;
use App\Http\Controllers\FiltersController;
use App\Http\Controllers\GatesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemTransferController;
use App\Http\Controllers\NewItemScan;
use App\Http\Controllers\NewItemScanProcessController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\PrivilegeEditController;
use App\Http\Controllers\RazerBatteryGoodBad;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RequiredListController;
use App\Http\Controllers\SendRegistrationInvitationController;
use App\Http\Controllers\SnLookOverEditController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\WarehouseLabelController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/set_language/{language}', function ($language) {
    Session::put('locale', $language);
    return redirect()->back();
})->name('set_language');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/test', function () {

})->name('test');


Route::get('/lists', [FiltersController::class, 'lists'])->name('lists')->middleware(['auth']);
Route::get('/goodsFlowItemFilterView', [FiltersController::class, 'goodsFlowItemFilterView'])->name('goodsFlowItemFilterView')->middleware(['auth']);

// Scrap/battery
Route::get('/RAZERBatteryGoodBad', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBad'])->name('RAZERBatteryGoodBad');
Route::get('/RAZERBatteryGoodBadDownload', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadDownload'])->name('RAZERBatteryGoodBadDownload');
Route::post('/RAZERBatteryGoodBadUpload', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadUpload'])->name('RAZERBatteryGoodBadUpload');
Route::post('/RAZERBatteryGoodBadAddConfirmation', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadAddConfirmation'])->name('RAZERBatteryGoodBadAddConfirmation');
Route::post('/RAZERBatteryGoodBadAdd', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadAdd'])->name('RAZERBatteryGoodBadAdd');
Route::post('/RAZERBatteryGoodBadEditSearch', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadEditSearch'])->name('RAZERBatteryGoodBadEditSearch');
Route::post('/RAZERBatteryGoodBadEditRZSelected', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadEditRZSelected'])->name('RAZERBatteryGoodBadEditRZSelected');
Route::post('/RAZERBatteryGoodBadSubmitEditedRZ', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadSubmitEditedRZ'])->name('RAZERBatteryGoodBadSubmitEditedRZ');
Route::post('/RAZERBatteryGoodBadSubmitRZForDelete', [RazerBatteryGoodBad::class, 'RAZERBatteryGoodBadSubmitRZForDelete'])->name('RAZERBatteryGoodBadSubmitRZForDelete');

// Calculator
Route::get('/calculator', [Calculator::class, 'calculatorView'])->name('calculatorView');
Route::post('/boxSearch/', [Calculator::class, 'boxSearch'])->name('boxSearch');
Route::get('/box/{id}', [Calculator::class, 'box'])->name('box');
Route::get('/print/boxLabel/{boxId}/{count}', [Calculator::class, 'boxLabel'])->name('boxLabel');
Route::post('startLookingSnFormats', [Calculator::class, 'startLookingSnFormats'])->name('startLookingSnFormats');
Route::post('startLookingSnDuplicationsInBox', [Calculator::class, 'startLookingSnDuplicationsInBox'])->name('startLookingSnDuplicationsInBox');
Route::post('startLookingSnDuplicationsInDatabase', [Calculator::class, 'startLookingSnDuplicationsInDatabase'])->name('startLookingSnDuplicationsInDatabase');
Route::get('getAutoStartStatus', [Calculator::class, 'getAutoStartStatus'])->name('getAutoStartStatus');
Route::get('toggleAutoStart', [Calculator::class, 'toggleAutoStart'])->name('toggleAutoStart');

//Box scan
Route::get('/scan/box/{id}', [BoxScanController::class, 'boxScan'])->name('boxScan');
Route::post('/scan/submitGf', [BoxScanController::class, 'submitGf'])->name('submitGf');
Route::post('/scan/getScannedGfIds', [BoxScanController::class, 'getScannedGfIds'])->name('getScannedGfIds');
Route::post('/scan/createBoxScan', [BoxScanController::class, 'createBoxScan'])->name('createBoxScan');
Route::post('/scan/getOpenScanIdForBox', [BoxScanController::class, 'getOpenScanIdForBox'])->name('getOpenScanIdForBox');
Route::post('/scan/getNeedToScanGFIds', [BoxScanController::class, 'getNeedToScanGFIds'])->name('getNeedToScanGFIds');
Route::post('/scan/getClosedScansForBox', [BoxScanController::class, 'getClosedScansForBox'])->name('getClosedScansForBox');
Route::post('/scan/closeScan', [BoxScanController::class, 'closeScan'])->name('closeScan');

//Skenavimu kurimas
Route::get('/newItemScan', [NewItemScan::class, 'newItemScan'])->name('newItemScan');
Route::post('/createNewItemScan', [NewItemScan::class, 'createNewItemScan'])->name('createNewItemScan');
Route::post('/createNewEmptyItemScan', [NewItemScan::class, 'createNewEmptyItemScan'])->name('createNewEmptyItemScan');
Route::get('/getNewScans', [NewItemScan::class, 'getNewScans'])->name('getNewScans');
Route::get('/getDoneScans', [NewItemScan::class, 'getDoneScans'])->name('getDoneScans');
Route::get('/getConfirmedScans', [NewItemScan::class, 'getConfirmedScans'])->name('getConfirmedScans');

//Nauju daiktu skenavimas
Route::get('/scan/{id}', [NewItemScanProcessController::class, 'GoToScan'])->name('GoToScan');
Route::get('/getNeedToScanSNs', [NewItemScanProcessController::class, 'getNeedToScanSNs'])->name('getNeedToScanSNs');
Route::get('/getScannedSNs', [NewItemScanProcessController::class, 'getScannedSNs'])->name('getScannedSNs');
Route::get('/getRegularPallets', [NewItemScanProcessController::class, 'getRegularPallets'])->name('getRegularPallets');
Route::get('/getDifferences', [NewItemScanProcessController::class, 'getDifferences'])->name('getDifferences');
Route::get('/closePallet', [NewItemScanProcessController::class, 'closePallet'])->name('closePallet');
Route::post('/makeScanDone', [NewItemScanProcessController::class, 'makeScanDone'])->name('makeScanDone');
Route::post('/makeScanNew', [NewItemScanProcessController::class, 'makeScanNew'])->name('makeScanNew');
Route::post('/makeScanConfirmed', [NewItemScanProcessController::class, 'makeScanConfirmed'])->name('makeScanConfirmed');
Route::post('/generateXlsx', [NewItemScanProcessController::class, 'generateXlsx'])->name('generateXlsx');
Route::post('/deleteXlsxFile', [NewItemScanProcessController::class, 'deleteXlsxFile'])->name('deleteXlsxFile');
Route::get('/downloadXlsxFile/{id}', [NewItemScanProcessController::class, 'downloadXlsxFile'])->name('downloadXlsxFile');
Route::get('/getScanData', [NewItemScanProcessController::class, 'getScanData'])->name('getScanData');
Route::post('/checkIfFileExists', [NewItemScanProcessController::class, 'checkIfFileExists'])->name('checkIfFileExists');
Route::get('/rewritePalletNumbers', [NewItemScanProcessController::class, 'rewritePalletNumbers'])->name('rewritePalletNumbers');
Route::get('/openPallet', [NewItemScanProcessController::class, 'openPallet'])->name('openPallet');
Route::get('/deletePallet', [NewItemScanProcessController::class, 'deletePallet'])->name('deletePallet');
Route::get('/getResults', [NewItemScanProcessController::class, 'getResults'])->name('getResults');
Route::post('/createRequiredListAudioFiles', [NewItemScanProcessController::class, 'createRequiredListAudioFiles'])->name('createRequiredListAudioFiles');
Route::post('/SNSubmit', [NewItemScanProcessController::class, 'SNSubmit'])->name('SNSubmit');
Route::post('/SNEanSubmit', [NewItemScanProcessController::class, 'SNEanSubmit'])->name('SNEanSubmit');
Route::post('/multipleNoSnSubmit', [NewItemScanProcessController::class, 'multipleNoSnSubmit'])->name('multipleNoSnSubmit');
Route::post('/scannedSnEdit', [NewItemScanProcessController::class, 'scannedSnEdit'])->name('scannedSnEdit');
Route::post('/deleteScannedProduct', [NewItemScanProcessController::class, 'deleteScannedProduct'])->name('deleteScannedProduct');
Route::post('/deleteScannedItems', [NewItemScanProcessController::class, 'deleteScannedItems'])->name('deleteScannedItems');
Route::get('/getRazerAPIStatus', [NewItemScanProcessController::class, 'getRazerAPIStatus'])->name('getRazerAPIStatus');
Route::get('/getRequiredListUsageStatus', [NewItemScanProcessController::class, 'getRequiredListUsageStatus'])->name('getRequiredListUsageStatus');
Route::get('/toggleRazerAPIStatus', [NewItemScanProcessController::class, 'toggleRazerAPIStatus'])->name('toggleRazerAPIStatus');
Route::get('/toggleRequiredListUsageStatus', [NewItemScanProcessController::class, 'toggleRequiredListUsageStatus'])->name('toggleRequiredListUsageStatus');
Route::post('/searchProductIdForScan', [NewItemScanProcessController::class, 'searchProductIdForScan'])->name('searchProductIdForScan');
Route::post('/createWarehouseLabelForPalletInScan', [NewItemScanProcessController::class, 'createWarehouseLabelForPalletInScan'])->name('createWarehouseLabelForPalletInScan');

//User settings

Route::get('/getVoiceList', [UserSettingsController::class, 'getVoiceList'])->name('getVoiceList');
Route::get('/getUsersVoiceSettings', [UserSettingsController::class, 'getUsersVoiceSettings'])->name('getUsersVoiceSettings');
Route::post('/changeUsersVoiceSettings', [UserSettingsController::class, 'changeUsersVoiceSettings'])->name('changeUsersVoiceSettings');
Route::get('/checkIfTestAudioExists', [UserSettingsController::class, 'checkIfTestAudioExists'])->name('checkIfTestAudioExists');

// Required list

Route::get('/requiredList', [RequiredListController::class, 'requiredList'])->name('requiredList');
Route::post('/requiredListUpload', [RequiredListController::class, 'requiredListUpload'])->name('requiredListUpload');
Route::get('/getActiveRequiredLists', [RequiredListController::class, 'getActiveRequiredLists'])->name('getActiveRequiredLists');
Route::get('/getActiveRequiredListIds', [RequiredListController::class, 'getActiveRequiredListIds'])->name('getActiveRequiredListIds');
Route::get('/getDisabledRequiredLists', [RequiredListController::class, 'getDisabledRequiredLists'])->name('getDisabledRequiredLists');
Route::post('/closeRequiredPallet', [RequiredListController::class, 'closeRequiredPallet'])->name('closeRequiredPallet');
Route::get('/downloadRequiredPalletXlsx/{id}', [RequiredListController::class, 'downloadRequiredPalletXlsx'])->name('downloadRequiredPalletXlsx');
Route::post('/openRequiredPallet', [RequiredListController::class, 'openRequiredPallet'])->name('openRequiredPallet');
Route::post('/deleteRequiredPallet', [RequiredListController::class, 'deleteRequiredPallet'])->name('deleteRequiredPallet');
Route::post('/getSelectedListPallets', [RequiredListController::class, 'getSelectedListPallets'])->name('getSelectedListPallets');
Route::post('/savePriorities', [RequiredListController::class, 'savePriorities'])->name('savePriorities');
Route::post('/selectRequiredListToDisplay', [RequiredListController::class, 'selectRequiredListToDisplay'])->name('selectRequiredListToDisplay');
Route::post('/editRequiredListQuantity', [RequiredListController::class, 'editRequiredListQuantity'])->name('editRequiredListQuantity');
Route::post('/deleteRequiredListProduct', [RequiredListController::class, 'deleteRequiredListProduct'])->name('deleteRequiredListProduct');
Route::post('/deleteRequiredListProducts', [RequiredListController::class, 'deleteRequiredListProducts'])->name('deleteRequiredListProducts');
Route::post('/RequiredListAddNewProduct', [RequiredListController::class, 'RequiredListAddNewProduct'])->name('RequiredListAddNewProduct');
Route::post('/editRequiredList', [RequiredListController::class, 'editRequiredList'])->name('editRequiredList');
Route::post('/createNewEmptyList', [RequiredListController::class, 'createNewEmptyList'])->name('createNewEmptyList');
Route::get('/getNewItemScanLockStatus', [RequiredListController::class, 'getNewItemScanLockStatus'])->name('getNewItemScanLockStatus');
Route::get('/lockNewItemScan', [RequiredListController::class, 'lockNewItemScan'])->name('lockNewItemScan');
Route::get('/unlockNewItemScan', [RequiredListController::class, 'unlockNewItemScan'])->name('unlockNewItemScan');
Route::post('/deactivateRequiredList', [RequiredListController::class, 'deactivateRequiredList'])->name('deactivateRequiredList');
Route::post('/activateRequiredList', [RequiredListController::class, 'activateRequiredList'])->name('activateRequiredList');
Route::post('/getRequiredListActivityStatus', [RequiredListController::class, 'getRequiredListActivityStatus'])->name('getRequiredListActivityStatus');
Route::post('/deleteRequiredList', [RequiredListController::class, 'deleteRequiredList'])->name('deleteRequiredList');
Route::post('/createWarehouseLabelForRequiredList', [RequiredListController::class, 'createWarehouseLabelForRequiredList'])->name('createWarehouseLabelForRequiredList');

//Send register invitation
Route::post('/sendRegisterInvitation', [SendRegistrationInvitationController::class, 'sendRegisterInvitation'])->name('sendRegisterInvitation');

//Registration
Route::post('/finishRegistration', [RegistrationController::class, 'finishRegistration'])->name('finishRegistration');
Route::get('/registerWithInvitation/{token}', [RegistrationController::class, 'registerWithInvitation'])->name('registerWithInvitation');

//Admin panel

Route::get('/adminPanel', [AdminPanelController::class, 'adminPanel'])->name('adminPanel');
Route::post('/setGatePhoneNumber', [AdminPanelController::class, 'setGatePhoneNumber'])->name('setGatePhoneNumber');
Route::get('/deleteGatePhoneNumber', [AdminPanelController::class, 'deleteGatePhoneNumber'])->name('deleteGatePhoneNumber');
Route::get('/getGatePhoneNumber', [AdminPanelController::class, 'getGatePhoneNumber'])->name('getGatePhoneNumber');
Route::get('/getVirtualPhoneNumber', [AdminPanelController::class, 'getVirtualPhoneNumber'])->name('getVirtualPhoneNumber');

//privilege edit

Route::post('/searchUserForPrivilegeEdit', [PrivilegeEditController::class, 'searchUserForPrivilegeEdit'])->name('searchUserForPrivilegeEdit');
Route::post('/getPrivilegeSetsForUser', [PrivilegeEditController::class, 'getPrivilegeSetsForUser'])->name('getPrivilegeSetsForUser');
Route::post('/removePrivilege', [PrivilegeEditController::class, 'removePrivilege'])->name('removePrivilege');
Route::post('/addPrivilege', [PrivilegeEditController::class, 'addPrivilege'])->name('addPrivilege');

//Box build

Route::get('/boxBuildMenu', [BoxBuildController::class, 'boxBuildMenu'])->name('boxBuildMenu');

//Box build maker
Route::get('/boxBuildMaker', [BoxBuildMakerController::class, 'boxBuildMaker'])->name('boxBuildMaker');
Route::post('/createBoxBuild', [BoxBuildMakerController::class, 'createBoxBuild'])->name('createBoxBuild');
Route::post('/addNewWarehouseCenter', [BoxBuildMakerController::class, 'addNewWarehouseCenter'])->name('addNewWarehouseCenter');
Route::get('/getAllWarehouseCenters', [BoxBuildMakerController::class, 'getAllWarehouseCenters'])->name('getAllWarehouseCenters');
Route::post('/getSelectedWarehouseData', [BoxBuildMakerController::class, 'getSelectedWarehouseData'])->name('getSelectedWarehouseData');
Route::post('/editWarehouseCenter', [BoxBuildMakerController::class, 'editWarehouseCenter'])->name('editWarehouseCenter');
Route::post('/deleteWarehouseCenter', [BoxBuildMakerController::class, 'deleteWarehouseCenter'])->name('deleteWarehouseCenter');
Route::post('/getAllBoxBuilds', [BoxBuildMakerController::class, 'getAllBoxBuilds'])->name('getAllBoxBuilds');
Route::post('/activateBoxBuild', [BoxBuildMakerController::class, 'activateBoxBuild'])->name('activateBoxBuild');
Route::post('/deActivateBoxBuild', [BoxBuildMakerController::class, 'deActivateBoxBuild'])->name('deActivateBoxBuild');

//Box build viewer
Route::get('/boxBuildViewer/{id}', [BoxBuildViewerController::class, 'boxBuildViewer'])->name('boxBuildViewer');
Route::get('/boxBuildList', [BoxBuildViewerController::class, 'boxBuildList'])->name('boxBuildList');
Route::get('/getBoxBuildRequiredItems/{id}', [BoxBuildViewerController::class, 'getBoxBuildRequiredItems'])->name('getBoxBuildRequiredItems');
Route::post('/getBoxBuildViewerBoxes', [BoxBuildViewerController::class, 'getBoxBuildViewerBoxes'])->name('getBoxBuildViewerBoxes');
Route::post('/getAllDirectScanBoxes', [BoxBuildViewerController::class, 'getAllDirectScanBoxes'])->name('getAllDirectScanBoxes');
Route::get('/directScanBoxes', [BoxBuildViewerController::class, 'directScanBoxes'])->name('directScanBoxes');
Route::post('/searchForPalletIds', [BoxBuildViewerController::class, 'searchForPalletIds'])->name('searchForPalletIds');
Route::post('/boxBuildFindItems', [BoxBuildViewerController::class, 'boxBuildFindItems'])->name('boxBuildFindItems');
Route::post('/getNeedToFindAmountsByManufacturer', [BoxBuildViewerController::class, 'getNeedToFindAmountsByManufacturer'])->name('getNeedToFindAmountsByManufacturer');
Route::post('/viewBoxItems', [BoxBuildViewerController::class, 'viewBoxItems'])->name('viewBoxItems');

//Box build edit
Route::post('/deleteItemFromBoxBuildList', [BoxBuildEditController::class, 'deleteItemFromBoxBuildList'])->name('deleteItemFromBoxBuildList');
Route::post('/editBoxBuildItem', [BoxBuildEditController::class, 'editBoxBuildItem'])->name('editBoxBuildItem');

//Box Build scanner
Route::get('/boxBuildScan', [BoxBuildScanController::class, 'boxBuildScan'])->name('boxBuildScan');
Route::post('/boxBuildSubmitGoodsFlowId', [BoxBuildScanController::class, 'boxBuildSubmitGoodsFlowId'])->name('boxBuildSubmitGoodsFlowId');
Route::post('/setDirectScanFulfilmentCenter', [BoxBuildScanController::class, 'setDirectScanFulfilmentCenter'])->name('setDirectScanFulfilmentCenter');
Route::get('/getFulfilmentCenters', [BoxBuildScanController::class, 'getFulfilmentCenters'])->name('getFulfilmentCenters');
Route::get('/getDirectScanFulfilmentCenter', [BoxBuildScanController::class, 'getDirectScanFulfilmentCenter'])->name('getDirectScanFulfilmentCenter');
Route::get('/getDirectScanStatus', [BoxBuildScanController::class, 'getDirectScanStatus'])->name('getDirectScanStatus');
Route::get('/toggleDirectScanStatus', [BoxBuildScanController::class, 'toggleDirectScanStatus'])->name('toggleDirectScanStatus');
Route::get('/prepareBoxBuildAudioFiles', [BoxBuildScanController::class, 'prepareBoxBuildAudioFiles'])->name('prepareBoxBuildAudioFiles');
Route::get('/getOpenBoxBuildBoxes', [BoxBuildScanController::class, 'getOpenBoxBuildBoxes'])->name('getOpenBoxBuildBoxes');
Route::post('/deleteBoxBuildBoxItem', [BoxBuildScanController::class, 'deleteBoxBuildBoxItem'])->name('deleteBoxBuildBoxItem');
Route::post('/deleteBoxBuildBox', [BoxBuildScanController::class, 'deleteBoxBuildBox'])->name('deleteBoxBuildBox');
Route::post('/closeBoxBuildBox', [BoxBuildScanController::class, 'closeBoxBuildBox'])->name('closeBoxBuildBox');
Route::post('/deleteClosedBoxBuildBox', [BoxBuildScanController::class, 'deleteClosedBoxBuildBox'])->name('deleteClosedBoxBuildBox');
Route::get('/boxBuildLabel/{id}', [BoxBuildScanController::class, 'boxBuildLabel'])->name('boxBuildLabel');

//Message form website form
Route::post('/SendContactUsMessage', [ContactUsFormController::class, 'sendContactUsMessage'])->name('sendContactUsMessage');

//Item transfer
Route::get('/itemTransfer', [ItemTransferController::class, 'itemTransfer'])->name('itemTransfer');
Route::post('/itemTransferSearchForBox', [ItemTransferController::class, 'itemTransferSearchForBox'])->name('itemTransferSearchForBox');
Route::post('/transferItems', [ItemTransferController::class, 'transferItems'])->name('transferItems');

//Gates
Route::get('/gateOpener', [GatesController::class, 'gateOpener'])->name('gateOpener');
Route::post('/makeCallToGates', [GatesController::class, 'makeCallToGates'])->name('makeCallToGates');

Route::post('/gatesCallback/rth87k4uil15632v1df9847894ikl156g1fQW941G8K41UIG98L6', function (\Illuminate\Http\Request $request) {
    broadcast(new GatesCallbackEvent($request->all()));
    return response();
})->name('gatesCallback');

//warehouse
Route::get('/warehouseLabel/{warehouseLabelKey}', [WarehouseLabelController::class, 'warehouseLabel'])->name('warehouseLabel');

//Sn Look Over edit
Route::get('/getGroups', [SnLookOverEditController::class, 'getGroups'])->name('getGroups');
Route::post('/createNewSnCheckGroup', [SnLookOverEditController::class, 'createNewSnCheckGroup'])->name('createNewSnCheckGroup');
Route::get('/deleteSnCheckGroup/{id}', [SnLookOverEditController::class, 'deleteSnCheckGroup'])->name('deleteSnCheckGroup');
Route::get('/activateSnCheckGroup/{id}', [SnLookOverEditController::class, 'activateSnCheckGroup'])->name('activateSnCheckGroup');
Route::get('/disableSnCheckGroup/{id}', [SnLookOverEditController::class, 'disableSnCheckGroup'])->name('disableSnCheckGroup');
Route::get('/getRulesForGroup/{id}', [SnLookOverEditController::class, 'getRulesForGroup'])->name('getRulesForGroup');
Route::post('/createNewSnCheckRule', [SnLookOverEditController::class, 'createNewSnCheckRule'])->name('createNewSnCheckRule');
Route::post('/deleteRule', [SnLookOverEditController::class, 'deleteRule'])->name('deleteRule');

//printer
Route::get('/printer', [PrinterController::class, 'printer'])->name('printer');
Route::post('/createWarehouseKeyAndDoPrint', [PrinterController::class, 'createWarehouseKeyAndDoPrint'])->name('createWarehouseKeyAndDoPrint');














