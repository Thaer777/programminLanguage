<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvincesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('provinces')->insert(
[
     ['name'=>'Damascus'],
     ['name'=>'Rif Dimashq'],
     ['name'=>'Aleppo'],
     ['name'=>'Idlib'],
     ['name'=>'Homs'],
     ['name'=>'Hama'],
     ['name'=>'Latakia'],
     ['name'=>'Tartus'],
     ['name'=>'Deir ez-Zor'],
     ['name'=>'Raqqa'],
     ['name'=>'Al-Hasakah'],
     ['name'=>'Daraa'],
    ['name'=>'As-Suwayda'],
    ['name'=>'Quneitra']
]
        );
    }
}
