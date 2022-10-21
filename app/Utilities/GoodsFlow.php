<?php

namespace App\Utilities;

use App\Models\Flow\Artikel;
use App\Models\Flow\Karton;

class GoodsFlow
{


    /**
     * Returns all RAZER items from goodsflow DB in an array ['rz' => 'name']
     * @return array
     */
    public static function getAllRazerArtikelsFromGoodsFlow(): array
    {
        $new = [];
        $items = Artikel::whereHersteller('RAZER')->get(['artikelnummer', 'name'])->toArray();
        foreach ($items as $item) {
            $new[$item['artikelnummer']] = $item['name'];
        }
        return $new;
    }

    /**
     * Returns all items from goodsflow DB in an array.
     * @return array
     */
    public static function getAllArtikelsFromGoodsFlow(): array
    {
        return Artikel::all(['artikelnummer', 'name'])->keyBy('artikelnummer')->toArray();

    }

    public static function getArtikelNameByRz($rz): string
    {
        return Artikel::whereArtikelnummer($rz)->first()->name;
    }

    public static function productExists($product): bool
    {
        return Artikel::whereArtikelnummer($product)->exists();
    }

    public static function loginToGoodsFlow()
    {
        $LOGINURL = config('app.gf_domain') . "/system/goodsflow/index.php?r=site/login";
        $agent = "Nokia-Communicator-WWW-Browser/2.0 (Geos 3.0 Nokia-9000i)";
        $POSTFIELDS = "LoginForm[username]=" . config('app.gf_user') . "&LoginForm[password]=" . config('app.gf_password') . "&LoginForm[rememberMe]=1";
        $reffer = config('app.gf_domain') . "/system/goodsflow/index.php?r=site/login";

        $ch = curl_init();

        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "Content-type: application/x-www-form-urlencoded;charset=UTF-8";

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $LOGINURL);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_REFERER, $reffer);
        curl_setopt($ch, CURLOPT_COOKIEFILE, app_path() . "cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, app_path() . "cookies.txt");

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            abort(422, $error_msg);
        }

        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus == 200) {
            return $result;
        } else {
            abort(422, 'Something went wrong while creating box in GoodsFlow (error 1)');
        }
    }

    public static function createBox($palletId, $boxName)
    {

        $LOGINURL = config('app.gf_domain') . "/system/goodsflow/index.php?r=testing/karton";
        $agent = "Nokia-Communicator-WWW-Browser/2.0 (Geos 3.0 Nokia-9000i)";
        $POSTFIELDS = "action=new&id[palette]=" . $palletId;
        $reffer = config('app.gf_domain') . "/system/goodsflow/index.php?r=testing/kartons&palette=" . $palletId;


        $ch = curl_init();

        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "Content-type: application/x-www-form-urlencoded;charset=UTF-8";

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $LOGINURL);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_REFERER, $reffer);
        curl_setopt($ch, CURLOPT_COOKIEFILE, app_path() . "cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, app_path() . "cookies.txt");

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            abort(422, $error_msg);
        }
        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus == 200) {

            $lines = explode("\n", $result);
            $boxname = str_replace(['OKAY, You got ', '<script type="text/javascript">'], "", $lines[0]);
            $karton = Karton::where("name", "=", $boxname)->firstOrFail();
            $karton->name = $boxName;
            $karton->timestamps = false;
            $karton->save();

            return $karton;

        } else {
            abort(422, 'Something went wrong while creating box in GoodsFlow (error 2)');
        }

    }
}
