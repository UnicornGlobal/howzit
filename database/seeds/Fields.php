<?php

use App\Field;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Fields extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Field::firstOrCreate([
            'id' => 1], [
            '_id' => 'cb3e3547-fa23-48b7-84ff-5f66b312f21b',
            'name' => 'email',
            'type' => 'email',
            'label' => 'Email Address',
            'required' => true,
            'min_length' => 7,
            'max_length' => 56,
            'regex' => '/(^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$)/',
            'order_index' => 1,
            'form_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Field::firstOrCreate([
            'id' => 2], [
            '_id' => '46939e46-2fd0-4053-b122-95a85b76cd0c',
            'name' => 'name',
            'regex' => null,
            'type' => 'text',
            'label' => 'Your Name',
            'required' => true,
            'min_length' => 2,
            'max_length' => 120,
            'order_index' => 3,
            'form_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Field::firstOrCreate([
            'id' => 3], [
            '_id' => 'f59d7f81-8a56-462c-976b-bb0c03c06107',
            'name' => 'product',
            'type' => 'text',
            'label' => 'Product that caught your interest',
            'required' => true,
            'min_length' => 2,
            'max_length' => 56,
            'regex' => '/\b(tabbs|everframe)\b/',
            'order_index' => 2,
            'form_id' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
