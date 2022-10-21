<?php

namespace App\Utilities;


use App\Models\NewItemScan\ItemToScan;
use App\Models\NewItemScan\ScrapItem;
use App\Models\NewItemScanPalletItem;
use App\Models\RazerBatteryGoodBad;
use App\Models\RequiredListPalletItem;
use Illuminate\Support\Carbon;

class XlsxGenerator
{

    private array $scanData;

    private array $borders = [1];

    private array $eans;

    private array $names;

    public function __construct($scanData)
    {
        $this->names = GoodsFlow::getAllRazerArtikelsFromGoodsFlow();
        $this->eans = $this->getEans();
        $this->scanData = $scanData;
    }

    public function getPallets()
    {
        $pallets = [];
        $itemCountInPallet = 1;
        foreach ($this->scanData['new_item_scan_pallets'] as $new_item_scan_pallet) {
            foreach ($new_item_scan_pallet['new_item_scan_pallet_items'] as $key => $item_scan_pallet_item) {
                $this->abortIfNoEan($item_scan_pallet_item['rz']);
                $itemCountInPallet = $itemCountInPallet + 1;
                $pallets[] = [
                    'ean' => $this->eans[$item_scan_pallet_item['rz']],
                    'rz' => $item_scan_pallet_item['rz'],
                    'box_name' => "RZ_KRT_" . Carbon::now()->year . "_" . strtoupper(Carbon::now()->shortEnglishMonth) . "_" . str_pad($new_item_scan_pallet['box_number'], 2, '0', STR_PAD_LEFT),
                    'rma' => $new_item_scan_pallet['text'],
                    'sn' => $item_scan_pallet_item['sn'],
                ];
            }
            $this->borders[] = $itemCountInPallet;
        }

        return $pallets;
    }

    public function getBorderPlaces(): array
    {
        return $this->borders;
    }

    public function getScraps()
    {
        $scraps = [];
        foreach ($this->scanData['scraps'] as $scrap) {
            $this->abortIfNoEan($scrap['rz']);
            $scraps[] = [
                'ean' => $this->eans[$scrap['rz']],
                'rz' => $scrap['rz'],
                'sn' => $scrap['sn'],
                'battery' => $scrap['scrap_pallet']['battery'] ? 'Battery' : '',
            ];
        }
        return $scraps;

    }

    public function getRequired()
    {
        $required = [];
        foreach ($this->scanData['required_list_pallet_items'] as $required_list_pallet_item) {
            $this->abortIfNoEan($required_list_pallet_item['rz']);
            $required[] = [
                'ean' => $this->eans[$required_list_pallet_item['rz']],
                'rz' => $required_list_pallet_item['rz'],
                'sn' => $required_list_pallet_item['sn'],
                'battery' => $required_list_pallet_item['required_list_pallet']['text'],
            ];
        }
        return $required;
    }

    public function getDifferences()
    {
        $calculated = [];
        $required = ItemToScan::whereNewItemScanId($this->scanData['id'])->get(['product_checked'])->toArray();
        $scanned = NewItemScanPalletItem::whereNewItemScanId($this->scanData['id'])->get(['rz'])->toArray();
        $scannedScrap = ScrapItem::whereNewItemScanId($this->scanData['id'])->get(['rz'])->toArray();
        $scannedRequiredItems = RequiredListPalletItem::whereNewItemScanId($this->scanData['id'])->get(['rz'])->toArray();

        foreach ($required as $item) {

            $item = $item['product_checked'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['required'] = $calculated[$item]['required'] + 1;
            } else {
                $calculated[$item]['name'] = '';
                $calculated[$item]['rz'] = $item;
                $calculated[$item]['required'] = 1;
                $calculated[$item]['scanned'] = 0;
            }

        }

        foreach ($scanned as $item) {

            $item = $item['rz'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['scanned'] = $calculated[$item]['scanned'] + 1;
            } else {
                $calculated[$item]['name'] = '';
                $calculated[$item]['rz'] = $item;
                $calculated[$item]['required'] = 0;
                $calculated[$item]['scanned'] = 1;
            }

        }

        foreach ($scannedScrap as $item) {

            $item = $item['rz'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['scanned'] = $calculated[$item]['scanned'] + 1;
            } else {
                $calculated[$item]['name'] = '';
                $calculated[$item]['rz'] = $item;
                $calculated[$item]['required'] = 0;
                $calculated[$item]['scanned'] = 1;
            }

        }

        foreach ($scannedRequiredItems as $item) {
            $item = $item['rz'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['scanned'] = $calculated[$item]['scanned'] + 1;
            } else {
                $calculated[$item]['name'] = '';
                $calculated[$item]['rz'] = $item;
                $calculated[$item]['required'] = 0;
                $calculated[$item]['scanned'] = 1;
            }

        }

        foreach ($calculated as $key => $item) {
            $calculated[$key]['difference'] = $calculated[$key]['scanned'] - $calculated[$key]['required'];
            $calculated[$key]['name'] = !empty($this->names[$key]) ? $this->names[$key] : 'Item not Found in GoodsFlow database';
        }

        return $calculated;
    }

    private function getEans(): array
    {
        $new = [];
        $items = RazerBatteryGoodBad::all(['rz', 'ean'])->toArray();
        foreach ($items as $item) {
            $new[$item['rz']] = $item['ean'];  //making artikelnummer as key
        }
        return $new;
    }

    public function getPalletsHeaders(): array
    {
        return ['EAN', 'Product', 'Box name', 'RMA', 'S/N'];
    }

    public function getScrapsHeaders(): array
    {
        return ['EAN', 'Product', 'S/N', 'Battery'];
    }

    public function getRequiredHeaders()
    {
        return ['EAN', 'Product', 'S/N', 'Box name'];
    }

    public function getDifferencesHeaders()
    {
        return ['Product name', 'Product', 'Required', 'Scanned', 'Difference'];
    }

    public function getRequiredPalletHeaders()
    {
        return ['EAN', 'Product', 'Box name', 'S/N'];
    }

    public function getRequiredPallet(): array
    {
        $data = [];
        foreach ($this->scanData['required_list_pallet_items'] as $required_list_pallet_item) {
            $this->abortIfNoEan($required_list_pallet_item['rz']);
            $data[] = [
                'ean' => $this->eans[$required_list_pallet_item['rz']],
                'rz' => $required_list_pallet_item['rz'],
                'name' => $this->scanData['text'],
                'sn' => $required_list_pallet_item['sn'],
            ];
        }
        return $data;
    }

    private function abortIfNoEan(string $rz): void
    {
        if (!array_key_exists($rz, $this->eans)) {
            abort(404, 'EAN for ' . $rz . ' not found');
        }
    }

}
