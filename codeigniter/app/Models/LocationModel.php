<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'report_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'report_no',
        'user_id',
        'is_anonymous',
        'title',
        'description',
        'incident_date',
        'category_id',
        'latitude',
        'longtitude',
        'address',
        'status',
        'moderation_status',
        'priority',
        'resolved_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'date_reported';
    protected $updatedField = 'updated_reported';
}
