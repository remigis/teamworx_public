<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Audios
{
    public static function deleteAllAudiosForRequiredListById($id): void
    {
        foreach (Storage::allFiles(Constants::AUDIO_FILE_PATH) as $file) {
            if (Str::startsWith($file, Constants::AUDIO_FILE_PATH . Constants::STRING_FOR_REQUIRED_LIST_AUDIO_NAME . $id . '-')) {
                Storage::delete($file);
            }
        }
    }

    public static function deleteAllAudiosForFulfilmentCenterById($id): void
    {
        foreach (Storage::allFiles(Constants::AUDIO_FILE_PATH) as $file) {
            if (Str::startsWith($file, Constants::AUDIO_FILE_PATH . Constants::STRING_FOR_BOX_BUILD_AUDIO_NAME . $id . '-')) {
                Storage::delete($file);
            }
        }
    }

    public static function deleteAllAudiosForUserById($id): void
    {
        foreach (Storage::allFiles(Constants::AUDIO_FILE_PATH) as $file) {
            if (Str::endsWith($file, '-' . Auth::user()->id . '.MP3')) {
                Storage::delete($file);
            }
        }
    }
}
