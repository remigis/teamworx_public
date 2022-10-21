<?php

namespace App\Utilities;

use App\Models\NewItemScan\ScannedSn;
use App\Models\NewItemScan\ScrapItem;
use App\Models\ScrapPallet as ScrapPalletModel;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ScrapPallet
{

    public function addToRazerScrapPallet(ScannedSn $sn, bool $battery)
    {
        if ($scrapPallet = ScrapPalletModel::whereType(Constants::SCRAP_PALLET_TYPE_RAZER)->whereClosed(false)->whereBattery($battery)->first()) {
            $this->writeToScrapPallet($sn, $scrapPallet);
        } else {
            $this->writeToScrapPallet($sn, $this->createScrapPallet(Constants::SCRAP_PALLET_TYPE_RAZER, $battery));
        }
    }

    private function writeToScrapPallet(ScannedSn $item, ScrapPalletModel $pallet)
    {
        $scrapItem = new ScrapItem();
        $scrapItem->sn = $item->sn;
        $scrapItem->rz = $item->rz;
        $scrapItem->new_item_scan_id = $item->new_item_scan_id;
        $scrapItem->user_id = $item->user_id;
        $scrapItem->scrap_pallet_id = $pallet->id;
        $scrapItem->scanned_sn_id = $item->id;

        $scrapItem->save();
    }

    public function createScrapPallet(string $type, bool $battery): ScrapPalletModel
    {
        Lock::db('scrap_pallets');
        $scrapPallet = new ScrapPalletModel();
        $scrapPallet->text = $this->scrapPalletTextGenerator($type);
        $scrapPallet->name = Str::random(12);
        $scrapPallet->type = $type;
        $scrapPallet->battery = $battery;
        $scrapPallet->save();
        Lock::remove();
        return $scrapPallet;

    }

    private function scrapPalletTextGenerator(string $type): string
    {
        return $type . '-SCRAP-' . Carbon::now()->toDateTime()->format('Ymd') . "-" . Str::upper(Str::random(3));

    }
}
