<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('Roles');
        $this->call('Users');
        $this->call('Forms');
        $this->call('Fields');
        $this->call('Responses');
    }
}
