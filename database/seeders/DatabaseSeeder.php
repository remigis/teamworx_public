<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         /*$this->call([
             PrivilegeSeeder::class, //privileges must be seeded before defaultUsers
             Locales::class,
             DefaultUsersSeeder::class,
             SettingsSeeder::class,
             VoicesSeeder::class,
         ]);*/

        $this->call([
            UpdateSeeder::class,
        ]);
    }
}
