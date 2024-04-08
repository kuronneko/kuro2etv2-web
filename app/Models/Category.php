<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name', 'user_id', 'created_at', 'updated_at'];

    public function file2es()
    {
        return $this->hasMany(File2e::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
