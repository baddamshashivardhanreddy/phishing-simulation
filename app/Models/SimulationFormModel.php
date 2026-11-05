<?php

namespace App\Models;

use CodeIgniter\Model;

class SimulationFormModel extends Model
{
    protected $table = 'simulation_forms';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'brand_name',
        'badge_text',
        'theme_color',
        'hero_title',
        'hero_description',
        'notice_text',
        'q1_label',
        'q1_placeholder',
        'q2_label',
        'q2_placeholder',
        'q3_label',
        'q3_placeholder',
        'q4_label',
        'q4_options',
        'submit_btn_text',
        'footer_text',
        'is_active',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

