<?php

namespace App\Utilities;

use App\Models\NewItemScan\ScannedSn;
use App\Models\RequiredList;
use App\Models\RequiredListItem;
use App\Models\RequiredListPallet;
use App\Models\RequiredListPalletItem;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RequiredPallet
{
    public function addToRequiredPallet(ScannedSn $sn, $required_list_id)
    {
        if ($requiredListPallet = RequiredListPallet::whereRequiredListId($required_list_id)->whereClosed(false)->first()) {
            $this->writeToRequiredPallet($sn, $required_list_id, $requiredListPallet);
        } else {
            $this->writeToRequiredPallet($sn, $required_list_id, $this->createRequiredListPallet($required_list_id));
        }
    }

    private function writeToRequiredPallet(ScannedSn $item, $required_list_id, RequiredListPallet $pallet)
    {
        $requiredListPalletItem = new RequiredListPalletItem();
        $requiredListPalletItem->sn = $item->sn;
        $requiredListPalletItem->rz = $item->rz;
        $requiredListPalletItem->new_item_scan_id = $item->new_item_scan_id;
        $requiredListPalletItem->user_id = $item->user_id;
        $requiredListPalletItem->required_list_pallet_id = $pallet->id;
        $requiredListPalletItem->required_list_id = $required_list_id;
        $requiredListPalletItem->scanned_sn_id = $item->id;

        $requiredListPalletItem->save();
    }

    public function createRequiredListPallet($required_list_id): RequiredListPallet
    {
        Lock::db('required_list_pallets');
        $requiredListPallet = new RequiredListPallet();
        $requiredListPallet->text = $this->requiredListPalletTextGenerator($required_list_id);
        $requiredListPallet->name = Str::random(12);
        $requiredListPallet->required_list_id = $required_list_id;
        $requiredListPallet->save();
        Lock::remove();
        return $requiredListPallet;

    }

    private function requiredListPalletTextGenerator($required_list_id): string
    {
        return RequiredList::whereId($required_list_id)->first()->name . "-" . Carbon::now()->toDateTime()->format('Ymd') . "-" . Str::upper(Str::random(3));
    }

    public function subtract($required_list_id, $rz): void
    {
        $item = RequiredListItem::whereRequiredListId($required_list_id)->whereRz($rz)->first();
        $item->count = $item->count - 1;
        $item->save();
    }

    public function add($required_list_id, $rz): void
    {
        $item = RequiredListItem::whereRequiredListId($required_list_id)->whereRz($rz)->first();
        $item->count = $item->count + 1;
        $item->save();
    }

}
