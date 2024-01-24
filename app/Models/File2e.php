<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File2e extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'file2es';

    protected $fillable = ['user_id', 'name', 'text', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
