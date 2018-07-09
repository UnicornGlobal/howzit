<?php

use App\Provider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MailProviders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        Provider::create([
            'id' => 1,
            '_id' => '6a6cfa4d-a8ce-40cb-aee2-ffe4dbb80aec',
            'name' => 'Mailgun',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
