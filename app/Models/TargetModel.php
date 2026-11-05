<?php

namespace App\Models;

use CodeIgniter\Model;

class TargetModel extends Model
{
    protected $table = 'targets';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email',
        'first_name',
        'last_name',
        'name',
        'department',
        'position',
        'sent',
        'opened',
        'clicked',
        'submitted',
        'submitted_data_payload',
        'reported',
        'reported_at',
        'last_activity'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
