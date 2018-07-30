<?php

use App\Form;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Forms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Form::firstOrCreate([
            'id' => 1], [
            '_id' =>'c1a440fe-0843-4da2-8839-e7ec6faee2c9',
            'name' => 'A well formed Form',
            'owner_email' => 'owner@howzit.com',
            'created_by' => 2,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
