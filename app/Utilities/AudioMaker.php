<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Storage;

class AudioMaker
{

    private string $url = 'https://ttsmp3.com/makemp3_new.php';

    private string|null $msg = 'Empty text';

    private string $voice = 'Aditi';

    private string $source = "ttsmp3";

    private int $userId;

    private string $assignment;


    public function __construct($msg, $voice, $pitch, $userId, $assignment)
    {
        $this->msg = "<prosody pitch='" . $pitch . "%'>" . $msg . "</prosody>";
        $this->voice = $voice;
        $this->userId = $userId;
        $this->assignment = $assignment;
    }

    private function getFileData()
    {

        $LOGINURL = $this->url;
        $agent = $this->getUserAgent();
        $POSTFIELDS = "msg=" . $this->msg . "&lang=" . $this->voice . "&source=" . $this->source;
        $reffer = "https://ttsmp3.com/";

        $ch = curl_init();

        $ip = $this->makeRandomIP();

        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "Content-type: application/x-www-form-urlencoded;charset=UTF-8";
        $headers[] = "X_FORWARDED_FOR: " . $ip;
        $headers[] = "REMOTE_ADDR: " . $ip;

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

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            print_r($error_msg);
        }

        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus == 200) {
            return json_decode($result);
        } else {
            return false;
        }
    }

    public function makeIfNotExists(): bool
    {
        if (!Storage::exists(self::path($this->userId, $this->assignment))) {
            return self::make();
        }
        return true;

    }


    public function make()
    {
        $fileData = $this->getFileData();
        if (!empty($fileData->Error)) {
            return false;
        }
        return Storage::put(self::path($this->userId, $this->assignment), file_get_contents($fileData->URL));
    }

    public static function path($userId, $assignment): string
    {
        return 'public/sounds/audio/' . $assignment . '-' . $userId . '.MP3';
    }

    private function makeRandomIP()
    {
        return "202.103.229." . rand(20, 150);
    }

    function getUserAgent()
    {
        $userAgentArray[] = "WinWAP/3.x (3.x.x.xx; Win32) (Google WAP Proxy/1.0)";

        $getArrayKey = array_rand($userAgentArray);
        return $userAgentArray[$getArrayKey];
    }
}
