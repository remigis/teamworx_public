<?php

namespace App\Utilities;

use App\Models\NewItemScan\NewItemScan;
use App\Models\NewItemScan\ScannedSn;
use App\Models\NewItemScanPallet;
use App\Models\NewItemScanPalletItem;
use Illuminate\Support\Str;

class RegularPallet
{
    public function addToRegularPallet(ScannedSn $sn): void
    {
        if ($pallet = NewItemScanPallet::whereClosed(false)->whereNewItemScanId($sn->new_item_scan_id)->orderBy('created_at', 'DESC')->first()) {
            $this->writeToPallet($sn, $pallet);
        } else {
            $this->writeToPallet($sn, $this->createRegularPallet($sn));
        }
    }

    private function writeToPallet(ScannedSn $item, NewItemScanPallet $pallet): void
    {
        $newItemScanPalletItem = new NewItemScanPalletItem();
        $newItemScanPalletItem->sn = $item->sn;
        $newItemScanPalletItem->rz = $item->rz;
        $newItemScanPalletItem->new_item_scan_id = $item->new_item_scan_id;
        $newItemScanPalletItem->user_id = $item->user_id;
        $newItemScanPalletItem->new_item_scan_pallet_id = $pallet->id;
        $newItemScanPalletItem->scanned_sn_id = $item->id;
        $newItemScanPalletItem->save();
    }

    public function createRegularPallet(ScannedSn $sn): NewItemScanPallet
    {
        Lock::db('new_item_scan_pallets');
        $newItemScanPallet = new NewItemScanPallet();
        $newItemScanPallet->text = $this->palletTextGenerator($sn->new_item_scan_id);
        $newItemScanPallet->name = Str::random(12);
        $newItemScanPallet->new_item_scan_id = $sn->new_item_scan_id;
        $newItemScanPallet->pallet_number = $this->getPalletNumber($sn->new_item_scan_id);
        $newItemScanPallet->save();
        Lock::remove();
        return $newItemScanPallet;

    }

    private function palletTextGenerator($newItemScanId): string
    {
        $newItemScan = NewItemScan::whereId($newItemScanId)->first();
        return $newItemScan->rma;
    }

    private function getPalletNumber($newItemScanId): int
    {
        return NewItemScanPallet::whereNewItemScanId($newItemScanId)->get()->count() + 1;
    }
}
