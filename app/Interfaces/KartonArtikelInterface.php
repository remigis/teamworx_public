<?php

namespace App\Interfaces;

use App\Models\Flow\KartonArtikel;
use Illuminate\Http\Request;

interface KartonArtikelInterface
{
    public function getAllKartonArtikels(KartonArtikel $kartonArtikel, Request $request, int $kartonId);
}





