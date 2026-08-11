<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';

    protected $returnType = 'array';

    protected $allowedFields = [
    'user_id',
    'report_id',
    'message',
    'status',
    'date'
];
    protected $useTimestamps = false;
}