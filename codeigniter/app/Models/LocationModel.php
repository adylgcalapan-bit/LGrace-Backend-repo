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
        'title',
        'description',
        'category_id',
        'latitude',
        'longitude',
        'address',
        'status',
        'moderation_status',
        'priority',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'report_date';
    protected $updatedField = 'updated_reported';
}