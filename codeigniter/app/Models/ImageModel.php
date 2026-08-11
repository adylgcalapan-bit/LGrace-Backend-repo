<?php

namespace App\Models;

use CodeIgniter\Model;

class ImageModel extends Model
{
    protected $table = 'image';
    protected $primaryKey = 'image_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'report_id',
        'image_path'
    ];
}