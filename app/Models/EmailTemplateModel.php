<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailTemplateModel extends Model
{
    protected $table = 'email_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'subject',
        'sender_name',
        'sender_email',
        'body_html'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

