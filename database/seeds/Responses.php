<?php

use App\Response;
use App\ResponseElement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Responses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Response::firstOrCreate([
            'id' => 1,
            '_id' =>'128010d0-a816-4322-bc23-2e431232cd5b',
            'form_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        ResponseElement::firstOrCreate([
            'id' => 2,
            'field_id' => 1,
            'response_id' => 1,
            'answer' => 'testuser@yahoo.com',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        ResponseElement::firstOrCreate([
            'id' => 3,
            'field_id' => 2,
            'response_id' => 1,
            'answer' => 'Tom Smith',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        ResponseElement::firstOrCreate([
            'id' => 1,
            'field_id' => 3,
            'response_id' => 1,
            'answer' => 'tabbs',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
