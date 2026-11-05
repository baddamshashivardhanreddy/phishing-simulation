<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizResultModel extends Model
{
    protected $table = 'quiz_results';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email',
        'score',
        'total',
        'passed',
        'q1_answer',
        'q2_answer',
        'q3_answer',
        'created_at'
    ];
    protected $useTimestamps = false;
}

