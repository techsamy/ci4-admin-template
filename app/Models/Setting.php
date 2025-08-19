<?php

namespace App\Models;

use CodeIgniter\Model;

class Setting extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'website_title', 'website_email', 'website_phone', 'website_meta_keywords',
        'website_meta_description', 'website_logo', 'website_favicon'
    ];
    
}
