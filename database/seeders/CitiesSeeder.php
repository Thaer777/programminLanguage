<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cities')->insert(
            [

        ['province_id'=>1,'name'=>'Damascus City'],
        ['province_id'=>1,'name'=>'al-Qanawt'],
        ['province_id'=>1,'name'=>'al-meidan'],
        ['province_id'=>1,'name'=>'Bab Tuma'],
        ['province_id'=>1,'name'=>'Saroujah'],
        ['province_id'=>2,'name'=>'Douma'],
        ['province_id'=>2,'name'=>'Al-Tall'],
        ['province_id'=>2,'name'=>'Al-Qutayfah'],
        ['province_id'=>3,'name'=>'Aleppo City'],
        ['province_id'=>3,'name'=>'Manbij'],
        ['province_id'=>3,'name'=>'Azaz'],
        ['province_id'=>4,'name'=>'Idlib City'],
        ['province_id'=>4,'name'=>'Maarrat al-Nuuman'],
        ['province_id'=>5,'name'=>'Homs City'],
        ['province_id'=>5,'name'=>'Al-Rastan'],
        ['province_id'=>6,'name'=>'Hama City'],
        ['province_id'=>6,'name'=>'Masyaf'],
        ['province_id'=>7,'name'=>'Latakia City'],
        ['province_id'=>7,'name'=>'Jableh'],
        ['province_id'=>8,'name'=>'Tartus City'],
        ['province_id'=>8,'name'=>'Baniyas'],
        ['province_id'=>9,'name'=>'Deir ez-Zor City'],
        ['province_id'=>9,'name'=>'Al-Mayadin'],
        ['province_id'=>10,'name'=>'Raqqa City'],
        ['province_id'=>10,'name'=>'Al-Thawrah'],
        ['province_id'=>11,'name'=>'Al-Hasakah City'],
        ['province_id'=>11,'name'=>'Qamishli'],
        ['province_id'=>12,'name'=>'Daraa City'],
        ['province_id'=>12,'name'=>'Al-Sanamayn'],
        ['province_id'=>13,'name'=>'As-Suwayda City'],
        ['province_id'=>13,'name'=>'Salkhad'],
        ['province_id'=>14,'name'=>'Quneitra City'],
        ['province_id'=>14,'name'=>'Fiq'],







            ]);
    }
}
