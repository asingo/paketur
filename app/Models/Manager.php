<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model
{
    use SoftDeletes, HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'managers';
    protected $fillable = [
        'id',
        'user_id',
        'company_id',
        'phone_number',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }
}
