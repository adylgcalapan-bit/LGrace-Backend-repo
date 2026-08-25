<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurokMapCoordinatesSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | VERIFIED / REPRESENTATIVE PUROK MAP ANCHORS
        |--------------------------------------------------------------------------
        | These coordinates are known points located inside or associated
        | with the corresponding Purok.
        |
        | They are used for Admin Map navigation only.
        | They are NOT claimed to be official geographic centroids.
        |--------------------------------------------------------------------------
        */

        $purokCoordinates = [

            'Purok Golden Gate' => [
                'latitude'  => '6.97824500',
                'longitude' => '125.08291200',
            ],




        ];

        foreach ($purokCoordinates as $purokName => $coordinates) {

            $purok = $this->db
                ->table('puroks')
                ->select('purok_id, purok_name, latitude, longitude')
                ->where('purok_name', $purokName)
                ->get()
                ->getRowArray();

            // Skip if Purok does not exist
            if (!$purok) {
                continue;
            }

            /*
             * Do not overwrite coordinates if they were already
             * manually/officially updated later.
             */
            if (
                $purok['latitude'] !== null &&
                $purok['latitude'] !== '' &&
                $purok['longitude'] !== null &&
                $purok['longitude'] !== ''
            ) {
                continue;
            }

            $this->db
                ->table('puroks')
                ->where('purok_id', $purok['purok_id'])
                ->update([
                    'latitude'   => $coordinates['latitude'],
                    'longitude'  => $coordinates['longitude'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }
}
