<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'report_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'is_anonymous',
        'title',
        'description',
        'category_id',
        'latitude',
        'longtitude',
        'address',
        'status',
        'priority',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'date_reported';
    protected $updatedField = 'updated_reported';
}
