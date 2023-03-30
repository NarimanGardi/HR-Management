<?php

namespace Database\Seeders;

use Database\Factories\JobsFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobsFactory::new()->count(20)->create();
    }
}
