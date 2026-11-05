<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignTargetModel extends Model
{
    protected $table = 'campaign_targets';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'campaign_id',
        'target_id',
        'email',
        'first_name',
        'last_name',
        'position',
        'department',
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

