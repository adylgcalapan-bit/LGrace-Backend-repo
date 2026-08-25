<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SaguingLocationsSeeder extends Seeder
{
    public function run()
    {
        $locations = [

            // =========================================
            // BARANGAY SAGUING
            // =========================================
            [
                'location_name'    => 'Barangay Saguing',
                'location_type'    => 'Barangay',
                'latitude'         => '6.97797000',
                'longitude'        => '125.08072000',
                'address'          => 'Barangay Saguing, Makilala, Cotabato',
                'search_keywords'  => 'saguing barangay saguing makilala cotabato',
                'is_active'        => 1,
            ],

            // =========================================
            // BARANGAY HALL
            // =========================================
            [
                'location_name'    => 'Saguing Barangay Hall',
                'location_type'    => 'Barangay Facility',
                'latitude'         => '6.97889000',
                'longitude'        => '125.07990000',
                'address'          => 'Barangay Saguing, Makilala, Cotabato',
                'search_keywords'  => 'saguing barangay hall barangay office government makilala',
                'is_active'        => 1,
            ],

            // =========================================
            // COVERED COURT
            // =========================================
            [
                'location_name'    => 'Saguing Covered Court',
                'location_type'    => 'Sports Facility',
                'latitude'         => '6.97885000',
                'longitude'        => '125.08004000',
                'address'          => 'Barangay Saguing, Makilala, Cotabato',
                'search_keywords'  => 'saguing covered court basketball court sports facility',
                'is_active'        => 1,
            ],

            // =========================================
            // ELEMENTARY SCHOOL
            // =========================================
            [
                'location_name'    => 'Saguing Elementary School',
                'location_type'    => 'School',
                'latitude'         => '6.98509000',
                'longitude'        => '125.08021000',
                'address'          => 'Saguing, Makilala, Cotabato 9401',
                'search_keywords'  => 'saguing elementary school school deped makilala',
                'is_active'        => 1,
            ],

            // =========================================
            // NATIONAL HIGH SCHOOL
            // =========================================
            [
                'location_name'    => 'Saguing National High School',
                'location_type'    => 'School',
                'latitude'         => '6.98583000',
                'longitude'        => '125.07881000',
                'address'          => 'Saguing, Malinawon, Makilala, Cotabato 9401',
                'search_keywords'  => 'saguing national high school snhs malinawon school deped',
                'is_active'        => 1,
            ],
        ];

        foreach ($locations as $location) {

            $builder = $this->db->table('saguing_locations');

            // Check if location already exists
            $existing = $builder
                ->where('location_name', $location['location_name'])
                ->get()
                ->getRowArray();

            $now = date('Y-m-d H:i:s');

            if ($existing) {

                // Update existing location instead of creating duplicate
                $location['updated_at'] = $now;

                $this->db
                    ->table('saguing_locations')
                    ->where('location_id', $existing['location_id'])
                    ->update($location);
            } else {

                // Insert new location
                $location['created_at'] = $now;
                $location['updated_at'] = $now;

                $this->db
                    ->table('saguing_locations')
                    ->insert($location);
            }
        }
    }
}
