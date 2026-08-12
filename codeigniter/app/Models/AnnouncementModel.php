<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'announcement_id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'title',
        'content',
        'category',
        'publish_date',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
