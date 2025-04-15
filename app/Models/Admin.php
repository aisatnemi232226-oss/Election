<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins'; // Ensure correct table name
    protected $primaryKey = 'admin_id'; // Set correct primary key
    public $incrementing = true; // Since it's AUTO_INCREMENT
    protected $keyType = 'int';
}
