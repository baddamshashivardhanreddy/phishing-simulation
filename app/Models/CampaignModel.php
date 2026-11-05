<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignModel extends Model
{
    protected $table = 'campaigns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'template_name',
        'include_report_link',
        'phish_server_url',
        'has_credential_form',
        'red_screen_duration',
        'show_debrief_screen',
        'status',
        'created_at',
        'completed_at'
    ];
    protected $useTimestamps = false;
}

