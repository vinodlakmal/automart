<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Sri Lanka districts with a few sample cities each.
        $data = [
            'Colombo'      => ['Colombo 1', 'Dehiwala', 'Moratuwa', 'Kotte', 'Maharagama', 'Homagama'],
            'Gampaha'      => ['Gampaha', 'Negombo', 'Ja-Ela', 'Wattala', 'Kelaniya', 'Minuwangoda'],
            'Kandy'        => ['Kandy', 'Peradeniya', 'Katugastota', 'Gampola', 'Nawalapitiya'],
            'Galle'        => ['Galle', 'Hikkaduwa', 'Ambalangoda', 'Karapitiya', 'Unawatuna'],
            'Kurunegala'   => ['Kurunegala', 'Kuliyapitiya', 'Mawathagama', 'Narammala'],
            'Jaffna'       => ['Jaffna', 'Nallur', 'Chavakachcheri', 'Point Pedro'],
            'Matara'       => ['Matara', 'Weligama', 'Akuressa', 'Dikwella'],
            'Anuradhapura' => ['Anuradhapura', 'Kekirawa', 'Medawachchiya'],
            'Badulla'      => ['Badulla', 'Bandarawela', 'Haputale', 'Welimada'],
            'Ratnapura'    => ['Ratnapura', 'Embilipitiya', 'Balangoda', 'Pelmadulla'],
        ];

        foreach ($data as $districtName => $cities) {
            $district = District::firstOrCreate(
                ['slug' => Str::slug($districtName)],
                ['name' => $districtName]
            );

            foreach ($cities as $cityName) {
                City::firstOrCreate(
                    ['district_id' => $district->id, 'slug' => Str::slug($cityName)],
                    ['name' => $cityName]
                );
            }
        }
    }
}
