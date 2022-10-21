<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoxLabelRequest;
use App\Http\Requests\BoxRequest;
use App\Models\Flow\Karton;
use App\Models\Flow\KartonArtikel;
use App\Models\Setting;
use App\Models\SnLookOverRule;
use App\Utilities\Constants;
use App\Utilities\PrivilegeUtilities;
use App\Utilities\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Calculator extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_USE_CALCULATOR]);
    }

    public function calculatorView()
    {
        return view('calculator.calculator');
    }

    public function getAutoStartStatus()
    {
        return response()->json((boolean)Setting::whereName(Constants::AUTO_LOOK_OVER_STATUS)->first()->value);
    }

    public function toggleAutoStart()
    {
        $autoStart = Setting::whereName(Constants::AUTO_LOOK_OVER_STATUS)->first();
        $autoStart->value = !$autoStart->value;
        $autoStart->save();
        return response()->json($autoStart->value);
    }

    public function boxSearch(Request $request)
    {

        $kartons = resolve(Karton::class)->leftJoin('flow_karton_artikel', 'flow_karton_artikel.karton_id', 'flow_karton.id')
            ->select(
                'flow_karton.id AS id',
                "flow_karton_artikel.gUID AS gUID",
                "flow_karton.name AS karton_name",
            )
            ->where(function ($query) use ($request) {
                $query->orWhere('flow_karton.name', 'LIKE', '%' . $request->searchString . '%')
                    ->orWhere('flow_karton_artikel.gUID', 'LIKE', '%' . $request->searchString . '%')
                    ->orWhere('flow_karton.id', 'LIKE', '%' . $request->searchString . '%');
            })
            ->groupBy('flow_karton.id')
            ->orderBy('flow_karton.id', 'DESC')
            ->get();

        foreach ($kartons as $karton) {
            $karton = $this->addLabel($karton, $request->searchString);
        }

        return response()->json($kartons);

    }

    public function startLookingSnFormats(Request $request)
    {
        $bad = [];
        $items = KartonArtikel::with(['statistiks' => function ($query) {
            $query->orderBy('timestamp', 'DESC');
        }, 'statistiks.user', 'artikel' => function ($query) {
            $query->select(['id', 'name', 'artikelnummer']);
        }])->whereKartonId($request->box_id)->get(['id', 'artikel_id', 'seriennummer', 'gUID'])->toArray();

        $regexes = array_column(SnLookOverRule::whereHas('snLookOverGroup', function (Builder $query) {
            $query->where('active', true);
        })->get(['regex'])->toArray(), 'regex');

        foreach ($items as $item) {
            if (Rule::fails($item, $regexes)) {
                $bad[] = $item;
            }
        }

        return response()->json($bad);

    }

    public function startLookingSnDuplicationsInBox(Request $request)
    {
        $bad = [];
        $items = KartonArtikel::whereKartonId($request->box_id)->get(['id', 'artikel_id', 'seriennummer', 'gUID'])->toArray();

        $snArray = array_column($items, 'seriennummer');
        $snArrayWithRemovedDuplications = array_unique(array_map("strtoupper", $snArray));

        $duplicatedSnArray = array_unique(array_map("strtoupper", array_diff_assoc(array_map("strtoupper", $snArray), $snArrayWithRemovedDuplications)));

        $duplicatedSnArray = array_diff(array_map("trim", $duplicatedSnArray), ['NO SN', 'NO S/N', null]);

        if (count($duplicatedSnArray) === 0) {
            return response()->json($bad);
        } else {
            foreach ($duplicatedSnArray as $duplicatedSn) {
                $bad[] = ['sn' => $duplicatedSn, 'items' => KartonArtikel::with(['statistiks' => function ($query) {
                    $query->orderBy('timestamp', 'DESC');
                }, 'statistiks.user', 'artikel' => function ($query) {
                    $query->select(['id', 'name', 'artikelnummer']);
                }])->whereSeriennummer($duplicatedSn)->whereKartonId($request->box_id)->get(['id', 'artikel_id', 'seriennummer', 'gUID'])->toArray()];
            }
        }

        return response()->json($bad);
    }

    public function startLookingSnDuplicationsInDatabase(Request $request)
    {
        $items = KartonArtikel::whereKartonId($request->box_id)->get(['id', 'artikel_id', 'seriennummer', 'gUID'])->toArray();

        $snArray = array_column($items, 'seriennummer');
        $snArrayWithRemovedDuplications = array_unique(array_map("strtoupper", $snArray));


        $snArrayWithRemovedDuplications = array_diff(array_map("trim", $snArrayWithRemovedDuplications), ['NO SN', 'NO S/N', null]);

        $bad = KartonArtikel::with(['statistiks' => function ($query) {
            $query->orderBy('timestamp', 'DESC');
        }, 'statistiks.user', 'artikel' => function ($query) {
            $query->select(['id', 'name', 'artikelnummer']);
        }])->whereIn('seriennummer', $snArrayWithRemovedDuplications)->where('karton_id', '!=', $request->box_id)->get(['id', 'artikel_id', 'seriennummer', 'gUID'])->toArray();


        return response()->json($bad);
    }

    private function addLabel($karton, $searchString)
    {
        $comma = "";
        $karton->gUID = strtoupper($karton->gUID);
        $searchString = strtoupper($searchString);

        if (!str_contains($karton->gUID, $searchString)) {
            $karton->gUID = '';
        } else {
            $comma = ", ";
        }

        $karton->label = $karton->id . ", " . $karton->karton_name . $comma . $karton->gUID;

        return $karton;
    }

    private function getBoxData($id)
    {
        $karton = resolve(Karton::class)
            ->where('flow_karton.id', '=', $id)
            ->with(['kartonArtikels', 'kartonArtikels.statistiks' => function ($query) {
                $query->orderBy('timestamp', 'DESC');
            }, 'kartonArtikels.statistiks.user', 'kartonArtikels.sphere', 'sphere', 'palette', 'palette.sphere', 'palette.kartons', 'kartonArtikels.liferant'])
            ->first();
        return $karton->toArray();
    }

    public function box(BoxRequest $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data['box'] = $this->getBoxData($request->id);
        $data['conditionACount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'A');
        $data['conditionBCount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'B');
        $data['conditionCCount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'C');
        $data['conditionDCount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'D');
        $data['conditionUCount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'U');
        $data['conditionSCount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'S');
        $data['conditionRCount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'R');
        $data['conditionXCount'] = $this->getItemCountByCondition($data['box']['karton_artikels'], 'X');
        $data['goodItems'] = $data['conditionACount'] + $data['conditionBCount'] + $data['conditionCCount'] + $data['conditionDCount'];
        $data['allItems'] = $data['goodItems'] + $data['conditionUCount'] + $data['conditionDCount'] + $data['conditionSCount'] + $data['conditionRCount'] + $data['conditionXCount'];
        $data['lieferantListString'] = $this->makeLieferentListString($data['box']['karton_artikels']);
        $data['compleationProcentage'] = $this->makeCompleationProcentage($data['box']['karton_artikels']);
        $data['deliveryString'] = $this->makeDeliveryString($data['box'], $data['lieferantListString']);
        Arr::set($data, 'box.palette.kartonsCount', $this->getPaletteBoxCount($data['box']));
        return view('calculator.box', $data);
    }

    private function getItemCountByCondition(array $items, string $condition): int
    {
        $count = 0;
        foreach ($items as $item) {
            if ($item['zustand'] == $condition) {
                $count++;
            }
        }
        return $count;
    }

    private function makeLieferentListString(array $items): string
    {
        $differentLieferentStringArray = [];
        foreach ($items as $item) {
            $differentLieferentStringArray[] = $item['liferant']['name'];
        }

        return implode(', ', array_unique($differentLieferentStringArray));
    }

    private function makeCompleationProcentage(array $items)
    {
        $tested = 0;
        foreach ($items as $item) {
            if ($item['zustand'] != "U") {
                $tested++;
            }
        }
        if (count($items) > 0) {
            return round(($tested * 100) / count($items), 2);
        } else {
            return 0;
        }


    }

    private function getPaletteBoxCount($box)
    {
        $count = 0;
        if ($box['palette'] !== null) {
            $count = count($box['palette']['kartons']);
        }
        return $count;
    }

    private function makeDeliveryString($box, $lieferant)
    {
        if ($box['palette'] != null) {
            $palette = '=HYPERLINK("' . env('GF_DOMAIN') . '/system/goodsflow/index.php?r=testing/kartons&palette=' . $box['palette']['id'] . '";"' . $box['palette']['name'] . '")';
        } else {
            $palette = "???";
        }
        $tab = "\t";
        return '=HYPERLINK("' . env('GF_DOMAIN') . '/system/goodsflow/index.php?r=testing/artikel&karton=' . $box['id'] . '";"' . $box['name'] . '")' . $tab . $lieferant . $tab . $tab . $palette;
    }

    public function boxLabel(BoxLabelRequest $request)
    {
        $data['count'] = $request->route('count');
        $data['boxId'] = $request->route('boxId');
        $data['box'] = Karton::with(['sphere', 'palette'])->whereId($request->route('boxId'))->first();
        return view('calculator/label', $data);
    }

}
