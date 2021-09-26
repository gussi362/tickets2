<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ttype;
class ttypesTableSeeder extends Seeder
{
   
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ttype::create([
            'id' => '1',
            'name_en' => 'pending',
            'name_ar' => 'في الانتظار',
            'set'   => 'orderStatus',
        ]);

        ttype::create([
            'id' => '2',
            'name_en' => 'cancled',
            'name_ar' => 'الغئ',
            'set'   => 'orderStatus'
        ]);

        ttype::create([
            'id' => '3',
            'name_en' => 'completed',
            'name_ar' => 'اكتمل',
            'set'   => 'orderStatus'
        ]);

        ttype::create([
            'id' => '4',
            'name_en' => 'success',
            'name_ar' => 'نجح',
            'set'   => 'payment'
        ]);

        ttype::create([
            'id' => '5',
            'name_en' => 'fail',
            'name_ar' => 'فشل',
            'set'   => 'payment'
        ]);
    }
}
