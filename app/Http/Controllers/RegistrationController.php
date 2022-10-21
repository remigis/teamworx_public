<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishRegistrationRequest;
use App\Models\Avatar;
use App\Models\Invitation;
use App\Models\User;
use App\Models\UserVoice;
use App\Models\Voices;
use App\Utilities\ProfilePicture;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{

    public function __construct()
    {
        $this->middleware(['guest']);
    }

    public function registerWithInvitation($token): Factory|View|Application
    {
        $invitation = Invitation::whereNull('registered_at')->whereToken($token)->firstOrFail(['name', 'email', 'token'])->toArray();
        return view('auth.registerWithInvitation', $invitation);
    }

    public function finishRegistration(FinishRegistrationRequest $request)
    {
        $invitation = Invitation::whereNull('registered_at')->whereToken($request->token)->firstOrFail();

        $user = new User();
        $user->name = $invitation->name;
        $user->password = Hash::make($request->password);
        $user->email = $invitation->email;
        $user->save();

        $picture = new ProfilePicture($user->name);
        $picture = $picture->make();

        $avatar = new Avatar();
        $avatar->name = $picture['avatar_name'];
        $avatar->path = $picture['avatar_path'];
        $avatar->user_id = $user->id;
        $avatar->save();

        $invitation->registered_at = Carbon::now()->toDateTime();
        $invitation->save();

        $userVoice = new UserVoice();
        $userVoice->user_id = $user->id;
        $userVoice->voice_name = Voices::inRandomOrder()->first()->name;
        $userVoice->pitch = rand(-100, 100);
        $userVoice->save();

        return response()->json(['status' => 'success'], 200);

    }
}
