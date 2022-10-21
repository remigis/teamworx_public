<?php

namespace App\Http\Controllers;

use App\Interfaces\KartonArtikelInterface;
use App\Models\Flow\KartonArtikel;
use App\Utilities\Config;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class FiltersController extends Controller
{
    protected KartonArtikelInterface $kartonArtikelRepository;

    protected array $operators = ["startsWith"];

    public function __construct(KartonArtikelInterface $kartonArtikelRepository)
    {
        $this->kartonArtikelRepository = $kartonArtikelRepository;
    }

    public function lists()
    {
        return view('lists');
    }

    public function goodsFlowItemFilterView()
    {
        return view('filters.goodsflow_item_filter');
    }

    private function columnRenameForMysql(string $column): string
    {
        $columns = [

            'artikel_name' => 'flow_artikel.name',
            'karton_id' => 'flow_karton_artikel.karton_id',
            'karton_name' => 'flow_karton.name',
            'artikelnummer' => 'flow_artikel.artikelnummer',
            'seriennummer' => 'flow_karton_artikel.seriennummer',
            'zustand' => 'flow_karton_artikel.zustand',
            'gUID' => 'flow_karton_artikel.gUID',
            'flow_user_name' => 'flow_user.name',
        ];

        return $columns[$column];
    }


    public function getFilteredGoodsflowItems(Request $request, $kartonId = null)
    {
        $filteredItems = $this->kartonArtikelRepository->getAllKartonArtikels(resolve(KartonArtikel::class), $request, $kartonId);


        foreach ($request->input('filters') as $column => $options) {
            if ($options['operator'] == 'and') {
                foreach ($options['constraints'] as $constraint) {
                    if (!empty($constraint['value'])) {
                        $filteredItems->where($this->columnRenameForMysql($column), $this->formatOperator($constraint['matchMode']), $this->formatValue($constraint['matchMode'], $constraint['value']));
                    }
                }
            }
            if ($options['operator'] == 'or') {
                $filteredItems->where(function ($query) use ($options, $column) {
                    foreach ($options['constraints'] as $constraint) {
                        if (!empty($constraint['value'])) {
                            $query->orWhere($this->columnRenameForMysql($column), $this->formatOperator($constraint['matchMode']), $this->formatValue($constraint['matchMode'], $constraint['value']));
                        }
                    }
                });
            }

        }


        if (!empty($request->input('multiSortMeta'))) {
            foreach ($request->input('multiSortMeta') as $sortFieldAndOrder) {
                $filteredItems = $filteredItems->orderBy($this->columnRenameForMysql($sortFieldAndOrder['field']), $this->getSortDirection($sortFieldAndOrder['order']));
            }
        } else {
            $filteredItems->orderBy('flow_statistik.timestamp', 'DESC');
        }


        $filteredItems = $filteredItems->paginate($request->input('rows'), ["*"], "page", $request->input('page'));


        foreach ($filteredItems as $filteredItem) {
            foreach ($filteredItem->statistiks as $statistik) {
                $statistik->timestamp = Config::timestampFromFlowToDate($statistik->timestamp);
            }

        }

        $copyStrings = $this->getCopyStrings($filteredItems->toArray());

        return response()->json(['data' => $filteredItems, 'copyStrings' => $copyStrings]);
    }

    private function getSortDirection($directionNumberFromVue): string
    {
        $dirrections = [
            "1" => 'ASC',
            "-1" => 'DESC'
        ];

        return $dirrections[$directionNumberFromVue];
    }

    private function formatValue($operator, $value): string
    {
        if ($operator == 'startsWith') {
            return $value . "%";
        }
        if ($operator == 'contains') {
            return "%" . $value . "%";
        }
        if ($operator == 'notContains') {
            return "%" . $value . "%";
        }
        if ($operator == 'endsWith') {
            return "%" . $value;
        }
        if ($operator == 'equals') {
            return $value;
        }
        if ($operator == 'notEquals') {
            return $value;
        } else {
            throw new Exception('Unknown operator' . $operator);
        }
    }

    private function formatOperator($operator): string
    {
        if ($operator == 'startsWith') {
            return "LIKE";
        }
        if ($operator == 'contains') {
            return "LIKE";
        }
        if ($operator == 'notContains') {
            return "NOT LIKE";
        }
        if ($operator == 'endsWith') {
            return "LIKE";
        }
        if ($operator == 'equals') {
            return "=";
        }
        if ($operator == 'notEquals') {
            return "!=";
        } else {
            throw new Exception('Unknown operator' . $operator);
        }
    }

    private function getCopyStrings(array $filteredItems): array
    {
        $copyStrings['allTable'] = '';
        $copyStrings['visibleTable'] = '';
        $copyStrings['allGoodsflows'] = '';
        $copyStrings['visibleGoodsflows'] = '';
        foreach ($filteredItems['data'] as $item) {
            $copyStrings['visibleGoodsflows'] .= $item['gUID'] . "\n";
            $copyStrings['visibleTable'] .= $item['artikel_name'] . "\t" . $item['artikelnummer'] . "\t" . $item['seriennummer'] . "\t" . $item['zustand'] . "\t" . $item['gUID'] . "\t" . $this->getNameFromItem($item) . "\t" . $item['kommentar'] . "\t" . $this->getTimestampFromItem($item) . "\n";

        }
        return $copyStrings;
    }

    private function getNameFromItem(array $item): string
    {
        if (!empty($item['statistiks'][0]['user']['name'])) {
            return $item['statistiks'][0]['user']['name'];
        } else {
            return "";
        }
    }

    private function getTimestampFromItem(array $item): string
    {
        if (!empty($item['statistiks'][0]['timestamp'])) {
            return $item['statistiks'][0]['timestamp'];
        } else {
            return "";
        }
    }
}
