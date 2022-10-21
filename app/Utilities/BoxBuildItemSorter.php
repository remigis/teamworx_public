<?php

namespace App\Utilities;

use App\Models\BoxBuildBox;
use App\Models\BoxBuildBoxItem;
use App\Models\BoxBuildRequiredItems;
use App\Models\Flow\KartonArtikel;
use App\Models\Setting;
use App\Models\WarehouseCenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BoxBuildItemSorter
{

    private string $goodsFlow;

    private bool $directScanStatus;

    private null|Builder|Model $kartonArtikel;

    private string $productCondition;

    private string $product;

    private WarehouseCenter|null $directScanCenter;

    private mixed $addedTo = null;

    public function __construct($goodsFlow)
    {
        $this->goodsFlow = $goodsFlow;
        $this->kartonArtikel = KartonArtikel::with(['artikel'])->where('gUID', '=', $this->goodsFlow)->first();
        $this->getDirectScanStatus();
        if ($this->directScanStatus) {
            $this->getDirectScanCenter();
            $this->addToBox($this->directScanCenter->id, $this->kartonArtikel);
        } else {
            $this->setProductPlusCondition();
            $this->getCentersThatNeedThisItem();
        }

    }

    public function getResultCenterId()
    {
        return $this->addedTo;
    }

    private function getDirectScanStatus(): void
    {
        $this->directScanStatus = (boolean)Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_STATUS)->first()->value;
    }

    private function getDirectScanCenter(): void
    {
        if ($center = WarehouseCenter::whereId(Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_CENTER)->first()->value)->first()) {
            $this->directScanCenter = $center;
        } else {
            $this->directScanCenter = null;
        }
    }

    private function setProductPlusCondition(): void
    {
        $this->productCondition = $this->kartonArtikel->artikel->artikelnummer . "_" . $this->kartonArtikel->zustand;
        $this->product = $this->kartonArtikel->artikel->artikelnummer;
    }

    private function getCentersThatNeedThisItem(): void
    {
        Lock::db(['box_build_boxes', 'box_build_required_items', 'box_build_box_items']);
        $found = false;

        $items = BoxBuildRequiredItems::whereHas('boxBuild', function ($query) { // if you need product with specific condition
            $query->where('active', '=', true);
        })->whereProductCondition($this->productCondition)->where('need', '>', 0)->orderBy('priority', 'ASC')->get()->toArray();
        if (count($items) > 0) {
            $this->addToBox($items[0]['warehouse_center_id'], $this->kartonArtikel, $items[0]['vid'], $items[0]['box_build_id'], $items[0]['product_condition'], false);
            $found = true;
        }

        if (!$found) {
            $items = BoxBuildRequiredItems::whereHas('boxBuild', function ($query) {  // if condition is not important
                $query->where('active', '=', true);
            })->whereProductCondition($this->product)->where('need', '>', 0)->orderBy('priority', 'ASC')->get()->toArray();
            if (count($items) > 0) {
                $this->addToBox($items[0]['warehouse_center_id'], $this->kartonArtikel, $items[0]['vid'], $items[0]['box_build_id'], $items[0]['product_condition']);
            }
        }


        Lock::remove();

    }

    private function addToBox(mixed $warehouse_center_id, $kartonArtikel, $vid = null, mixed $box_build_id = null, mixed $product_condition = null, $conditionNotImportant = true)
    {
        Lock::db(['box_build_boxes', 'box_build_required_items', 'box_build_box_items']);

        $box = BoxBuildBox::firstOrCreate(
            ['box_build_id' => $box_build_id, 'warehouse_center_id' => $warehouse_center_id, 'active' => true],
            ['name' => $this->generateName($warehouse_center_id)]
        );

        BoxBuildBoxItem::create(
            [
                'product' => $kartonArtikel->artikel->artikelnummer,
                'condition' => $kartonArtikel->zustand,
                'gf' => $kartonArtikel->gUID,
                'vid' => $vid,
                'box_build_box_id' => $box->id,
                'condition_not_important' => $conditionNotImportant,
            ]
        );


        if (!$this->directScanStatus) {
            $query = BoxBuildRequiredItems::whereWarehouseCenterId($warehouse_center_id)
                ->whereBoxBuildId($box_build_id)
                ->whereProductCondition($product_condition)
                ->where('need', '>', 0)->first();

            $query->decrement('need');
            $query->increment('collected');
        }
        Lock::remove();

        $this->addedTo = $warehouse_center_id;
    }

    private function generateName(mixed $warehouse_center_id): string
    {
        $prefix = WarehouseCenter::whereId($warehouse_center_id)->first()->box_prefix;
        return $prefix . '_' . Carbon::now()->format('YmdHi') . '_' . strtoupper(Str::random(3));
    }


}
