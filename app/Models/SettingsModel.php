<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'smtp_host',
        'smtp_user',
        'smtp_pass',
        'smtp_port',
        'smtp_crypto',
        'brevo_api_key',
        'from_email',
        'from_name',
        'admin_server_url',
        'phish_server_url',
        'cloak_phish_domain',
        'admin_bypass_key'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
