<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class responseCodesSeeder extends Seeder
{
    //200+ should indcate the request was made successfully
    //400+ should indacte the request failuer from the user part
    //500+ should indacte the request failuer from the system part

    /*for crud only 
        //n00 is reserved for reading success of failuer
        //n01 is reserved for creation success ,or failuer
        //n02 is reserved for update success or failuer
        //n03 is reserved for destorying success of failuer
    */

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        //PASSPORT 
        DB::table('response_codes')->insert([
            'response_code' => '204',
            'response_description' => 'user logged in successfully',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '205',
            'response_description' => 'changed password successfully',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '206',
            'response_description' => 'reset password successfully',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '207',
            'response_description' => 'registered successfully',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '220',
            'response_description' => 'sent reset code successfully',
        ]);


        DB::table('response_codes')->insert([
            'response_code' => '411',
            'response_description' => 'invalid credintals',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '412',
            'response_description' => 'email doesn\'t exists',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '413',
            'response_description' => 'invalid reset code',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '414',
            'response_description' => 'old and new passwords match',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '415',
            'response_description' => 'wrong old password',
        ]);

        //CRUD SUCCESS
        DB::table('response_codes')->insert([
            'response_code' => '200',
            'response_description' => 'resources found successfully',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '201',
            'response_description' => 'resources created successfully',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '202',
            'response_description' => 'resources updated successfully',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '203',
            'response_description' => 'resources deleted successfully',
        ]);

        //CRUD FAIL
        DB::table('response_codes')->insert([
            'response_code' => '500',
            'response_description' => 'resources not found',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '501',
            'response_description' => 'failed to create resource',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '502',
            'response_description' => 'failed to update resource',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '503',
            'response_description' => 'failed to delete resource',
        ]);

        //

        //UNAUTHORIZED
        DB::table('response_codes')->insert([
            'response_code' => '401',
            'response_description' => 'user is not authorized to do opreation',
        ]);

        //System error
        DB::table('response_codes')->insert([
            'response_code' => '510',
            'response_description' => 'internal server error',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '410',
            'response_description' => 'invalid input data',
        ]);

        //Orders
        DB::table('response_codes')->insert([
            'response_code' => '430',
            'response_description' => 'insufficient tickets',
        ]);

        DB::table('response_codes')->insert([
            'response_code' => '431',
            'response_description' => 'tickets aren\'t paid for',
        ]);

    }
}
