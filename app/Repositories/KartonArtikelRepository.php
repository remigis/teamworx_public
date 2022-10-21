<?php

namespace App\Repositories;

use App\Interfaces\KartonArtikelInterface;
use Illuminate\Database\Eloquent\Builder;


class KartonArtikelRepository implements KartonArtikelInterface
{

    private $withBoxId = false;

    public function getAllKartonArtikels($kartonArtikel, $request, $kartonId): Builder
    {
        $kartonArtikel = $kartonArtikel->leftJoin('flow_artikel', 'flow_artikel.id', 'flow_karton_artikel.artikel_id')
            ->leftJoin('flow_karton', 'flow_karton.id', 'flow_karton_artikel.karton_id')
            ->leftJoin('flow_statistik', 'flow_statistik.karton_artikel_id', '=', 'flow_karton_artikel.id')
            ->leftJoin('flow_user', 'flow_user.id', 'flow_statistik.user_id')
            ->select(
                "flow_karton_artikel.id",
                "flow_karton_artikel.artikel_id",
                "flow_karton_artikel.karton_id",
                "flow_karton_artikel.seriennummer",
                "flow_karton_artikel.zustand AS zustand",
                "flow_karton_artikel.gUID AS gUID",
                "flow_karton_artikel.status",
                "flow_karton_artikel.palette_id",
                "flow_karton_artikel.sphere_id",
                "flow_karton_artikel.kommentar",
                "flow_karton.id AS karton_karton_id",
                "flow_karton.name AS karton_name",
                "flow_karton.sphere_id AS karton_sphere_id",
                "flow_karton.palette_id AS karton_palette_id",
                "flow_karton.status AS karton_status",
                "flow_karton.kommentar AS karton_kommentar",
                "flow_karton.metadata_id AS karton_metadata_id",
                "flow_artikel.name AS artikel_name",
                "flow_artikel.artikelnummer",
                "flow_statistik.timestamp AS flow_statistik_timestamp",
                "flow_statistik.zustand AS flow_statistik_zustand",
                "flow_user.name AS flow_user_name",
            )
            ->groupBy('flow_karton_artikel.id')
            ->with(['statistiks' => function ($query) {
                $query->orderBy('timestamp', 'DESC');
            }, 'statistiks.user', 'sphere']);

        if ($kartonId !== null) {
            $kartonArtikel = $kartonArtikel->where('flow_karton_artikel.karton_id', '=', $kartonId);
            $this->withBoxId = true;
        }

        if (!empty($request->input('boxId'))) {
            $kartonArtikel = $kartonArtikel->where('flow_karton_artikel.karton_id', '=', $request->input('boxId'));
            $this->withBoxId = true;
        }

        if ($this->withBoxId === false) {
            $kartonArtikel = $kartonArtikel->where('flow_karton_artikel.karton_id', '>=', 14000);
        }


        return $kartonArtikel;

    }
}
