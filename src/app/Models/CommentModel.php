<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table         = 'comments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'text', 'date'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'name' => 'required|valid_email|max_length[255]',
        'text' => 'required|min_length[1]',
        'date' => 'required|valid_date[Y-m-d]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'    => 'Email обязателен.',
            'valid_email' => 'Введите корректный email.',
            'max_length'  => 'Email слишком длинный.',
        ],
        'text' => [
            'required'   => 'Текст комментария обязателен.',
            'min_length' => 'Комментарий не может быть пустым.',
        ],
        'date' => [
            'required' => 'Дата обязательна.',
        ],
    ];
}