<?php

namespace Database\Seeders;

use App\Models\ContactMethod;
use Illuminate\Database\Seeder;

class ContactMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContactMethod::query()->truncate();
        ContactMethod::factory(4)->create();
    }
}
