<?php

namespace Database\Seeders;

use App\Models\Avatar;
use App\Models\User;
use App\Models\UserVoice;
use App\Utilities\PrivilegeUtilities;
use App\Utilities\ProfilePicture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Remigijus Šimkus';
        $user->password = Hash::make('password');
        $user->email = 'remigijus136@gmail.com';
        $user->save();

        PrivilegeUtilities::giveAllPrivilegesToUser($user);

        $picture = new ProfilePicture($user->name);
        $picture = $picture->make();

        $avatar = new Avatar();
        $avatar->name = $picture['avatar_name'];
        $avatar->path = $picture['avatar_path'];
        $avatar->user_id = $user->id;
        $avatar->save();

        $userVoice = new UserVoice();
        $userVoice->user_id = $user->id;
        $userVoice->voice_name = 'Geraint';
        $userVoice->pitch = 0;
        $userVoice->save();


    }
}
