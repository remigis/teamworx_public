<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeUsersVoiceSettingsRequest;
use App\Models\UserVoice;
use App\Models\Voices;
use App\Utilities\AudioMaker;
use App\Utilities\Audios;
use App\Utilities\Constants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserSettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function getVoiceList()
    {
        return response()->json(Voices::all(['name', 'location', 'label'])->toArray());
    }

    public function getUsersVoiceSettings()
    {
        $usersVoice = UserVoice::whereUserId(Auth::user()->id)->first(['voice_name', 'pitch']);
        return response()->json(['selectedVoice' => Voices::whereName($usersVoice->voice_name)->first(['name', 'location', 'label']), 'pitchValue' => $usersVoice->pitch]);
    }

    public function changeUsersVoiceSettings(ChangeUsersVoiceSettingsRequest $request)
    {
        $userVoice = UserVoice::whereUserId(Auth::user()->id)->first();
        $userVoice->voice_name = $request->name;
        $userVoice->pitch = $request->pitch;
        Audios::deleteAllAudiosForUserById(Auth::user()->id);

        if ($userVoice->save() && $this->makeTestAudioFile($userVoice)) {
            return response()->json(['message' => 'Voice changed']);
        } else {
            return response()->json(['message' => 'Voice change failed'], 422);
        }
    }

    private function makeTestAudioFile($userVoice)
    {
        $testAudio = new AudioMaker('Hello. This is my new voice', $userVoice->voice_name, $userVoice->pitch, Auth::user()->id, Constants::TEST_AUDIO_ASSIGNMENT);
        return $testAudio->make();
    }

    public function checkIfTestAudioExists()
    {
        $exists = Storage::exists(Constants::AUDIO_FILE_PATH . Constants::TEST_AUDIO_ASSIGNMENT . '-' . Auth::user()->id . '.MP3');
        return response()->json(['isInStorage' => $exists]);
    }

}
