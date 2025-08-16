<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class payroll extends Model
{
    protected $fillable = [
        'empno',
        'period',
        'basicsalary',
        'transport',
        'meal',
        'overtime',
        'medical',
        'hospital',
        'kacamata',
        'tooth',
        'premi',
        'komisi',
        'masabakti',
        'thr',
        'otherincome',
        'othremark',
        'rumah',
        'jabatan',
        'listrik',
        'leave',
        'sanksi',
        'fixedtax',
        'jkm',
        'jkk',
        'jht',
        'bpjskaryawan',
        'bpjsperusahaan',
        'refund',
        'yayasan',
        'personaladvance',
        'koperasi',
        'businessadvance',
        'loancar',
        'loanother',
        'credit',
        'milenium',
        'grossincome',
        'netincomeexpph22',
        'taxamonth',
        'total',
        'thp',
      ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //public function emp_master(): BelongsTo
    //{
      //  return $this->belongsTo(emp_master::class);
    //}
}
