<?php

use Phinx\Seed\AbstractSeed;
use Faker\Factory;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $data = array_map(function ($index) use ($faker) {
            return [
                'username' => $faker->userName(),
                'email' => $faker->safeEmail(),
                'password' => password_hash('password', PASSWORD_BCRYPT),
            ];
        }, range(0, 4));

        $users = $this->table('users');
        $users->insert($data)
            ->save();
    }
}
