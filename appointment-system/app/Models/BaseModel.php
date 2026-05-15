<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

abstract class BaseModel
{
    protected $db;

    public function __construct()
    {
        $this->db = db();
    }
}
