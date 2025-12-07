<?php

namespace Database\Seeders;

use App\Models\Amenitie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $amenities =  [
        'WiFi',
        'Air Conditioning',
        'Swimming Pool',
        'Gym',
        'Parking',
        'Pet Friendly',
        'Breakfast Included',
        'Airport Shuttle',
        'Spa',
        'Restaurant',
        'Bar',
        'Laundry Service',
        'Room Service',
        '24-Hour Front Desk',
        'Business Center',
        'Conference Rooms',
       ];
         foreach($amenities as $amenityName){
          Amenitie::create(['name' => $amenityName]);
         }
    }
}
