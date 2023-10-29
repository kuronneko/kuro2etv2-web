<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File2e extends Model
{
    use HasFactory;

    protected $table = 'file2es';

    protected $fillable = ['user_id', 'name', 'text'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
