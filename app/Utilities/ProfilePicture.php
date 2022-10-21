<?php

namespace App\Utilities;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

class ProfilePicture
{
    private string $name;

    private InitialAvatar $avatar;

    public function __construct($name)
    {
        $this->name = $name;
        $this->avatar = new InitialAvatar();
    }


    #[ArrayShape(['avatar_name' => "string", 'avatar_path' => "string"])] public function make(): array
    {
        $image = $this->avatar->autoFont()->name($this->name)->background($this->color())->color('#fff')->size(600)->generate()->stream('png', 100);
        $avatar_name = Str::random(55);
        $avatar_path = '/avatars/' . $avatar_name . '.png';
        Storage::put('/public' . $avatar_path, $image);
        return ['avatar_name' => $avatar_name, 'avatar_path' => $avatar_path];
    }

    function color(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}
