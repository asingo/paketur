<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'companies';
    protected $fillable = [
        'name'
    ];
    public function employees(){
        return $this->hasMany(Employee::class);
    }

    public function manager(){
        return $this->hasMany(Manager::class);
    }
}
