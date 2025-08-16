<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class emp_master extends Model
{
    use HasFactory;

    protected $table = 'emp_masters'; // Specify table name

    protected $fillable = [
        'empno',
        'fullname',
        'user_id',
        'city_id',
        'nationality_id',
        'gender_id',
        'maritalstatus_id',
        'religion_id',
    ];

    // Relationship back to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional: relationships untuk foreign keys lain
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class, 'maritalstatus_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }
}