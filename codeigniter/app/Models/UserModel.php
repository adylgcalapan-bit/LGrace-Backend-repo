<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'full_name',
        'email',
        'mobile_number',
        'username',
        'address',
        'profile_image',
        'password',
        'purok_id',
        'role',
        'is_active',
        'email_verified_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
