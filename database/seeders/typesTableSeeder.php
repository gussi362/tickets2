<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\type;

class typesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        type::create([
            'id' => '1',
            'type' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        type::create([
            'id' => '2',
            'type' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        type::create([
            'id' => '3',
            'type' => 'qr',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

    }
}
