<?php

namespace App\Models;

use CodeIgniter\Model;

class TrackingLogModel extends Model
{
    protected $table = 'tracking_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'campaign_id',
        'email',
        'action',
        'details',
        'ip_address',
        'user_agent',
        'created_at'
    ];
    protected $useTimestamps = false;
}

