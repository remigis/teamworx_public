<?php

namespace App\Http\Controllers;

use App\Events\BoxBuildDirectScanCenterChangeEvent;
use App\Http\Requests\AddNewWarehouseCenterRequest;
use App\Http\Requests\CreateBoxBuildRequest;
use App\Http\Requests\DeleteWarehouseCenterRequest;
use App\Http\Requests\EditWarehouseCenterRequest;
use App\Http\Requests\GetSelectedWarehouseDataRequest;
use App\Models\BoxBuild;
use App\Models\WarehouseCenter;
use App\Utilities\Audios;
use App\Utilities\BoxBuildMakerUtilities;
use App\Utilities\Lock;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoxBuildMakerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_CREATE_NEW_BOX_BUILDS]);
    }

    public function boxBuildMaker()
    {
        return view('boxBuild.boxBuildMaker');
    }

    public function createBoxBuild(CreateBoxBuildRequest $request)
    {
        $boxBuild = new BoxBuildMakerUtilities($request);
        if ($boxBuild->make()) {
            return response()->json(['message' => 'Box-Build created']);
        } else {
            return response()->json(['message' => 'Failed to create Box-Build'], 422);
        }
    }

    public function addNewWarehouseCenter(AddNewWarehouseCenterRequest $request): JsonResponse
    {
        $request->merge(['box_prefix' => strtoupper($request->box_prefix)]);

        WarehouseCenter::create($request->all());
        return response()->json(['message' => 'New warehouse center added']);
    }

    public function editWarehouseCenter(EditWarehouseCenterRequest $request): JsonResponse
    {

        $center = WarehouseCenter::whereId($request->id)->first();
        if ($center->audio_text !== $request->input('audio_text')) {
            $center->update($request->all(['name', 'audio_text', 'box_prefix', 'pallet_id']));
            Audios::deleteAllAudiosForFulfilmentCenterById($center->id);
            broadcast(new BoxBuildDirectScanCenterChangeEvent());
        } else {
            $center->update($request->all(['name', 'audio_text', 'box_prefix', 'pallet_id']));
        }

        return response()->json(['message' => 'Warehouse updated']);
    }

    public function deleteWarehouseCenter(DeleteWarehouseCenterRequest $request): JsonResponse
    {
        WarehouseCenter::whereId($request->id)->delete();
        return response()->json(['message' => 'Warehouse deleted']);
    }

    public function getAllWarehouseCenters(): JsonResponse
    {
        return response()->json(WarehouseCenter::all(['id', 'name', 'audio_text'])->toArray());
    }

    public function getSelectedWarehouseData(GetSelectedWarehouseDataRequest $request): JsonResponse
    {
        return response()->json(WarehouseCenter::whereId($request->id)->first(['id', 'name', 'audio_text', 'box_prefix', 'pallet_id'])->toArray());
    }

    public function getAllBoxBuilds(Request $request)
    {
        return response()->json(BoxBuild::orderBy('created_at', 'DESC')->paginate($request->rows, ['*'], 'page', $request->page));
    }

    public function activateBoxBuild(Request $request)
    {
        Lock::db('box_builds');

        if (BoxBuild::whereActive(true)->count() > 0) {
            Lock::remove();
            return response()->json(['message' => "You can't have more than one active Box-Build"], 422);
        }

        BoxBuild::whereId($request->id)->update(['active' => true]);
        Lock::remove();

        return response()->json(['message' => 'Box build activated']);
    }

    public function deActivateBoxBuild(Request $request)
    {
        Lock::db('box_builds');
        BoxBuild::whereId($request->id)->update(['active' => false]);
        Lock::remove();
        return response()->json(['message' => 'Box build deactivated']);
    }
}
