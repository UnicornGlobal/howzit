<?php

use App\Credentials;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Webpatser\Uuid\Uuid;

class MailCredentials extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Credentials::create([
            'id' => 1,
            '_id' => '2d6adc3f-6b0c-4b3f-a303-6b694f4776f0',
            'name' => 'seeded creds',
            'user_id' => 2,
            'provider_id' => 1,
            'username' => 'mailgun_username',
            'secret' => Crypt::encrypt('59095b71-9f52-41f1-9211-46da331b8b02'),
            'domain' => 'howzit',
            'mail_from_address' => 'test@howzit.com',
            'mail_from_name' => 'Howzit Seed',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
