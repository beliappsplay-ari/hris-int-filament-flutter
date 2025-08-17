<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class payroll extends Model
{
    protected $fillable = [
        'empno', 'period', 'basicsalary', 'transport', 'meal', 'overtime',
        'medical', 'hospital', 'kacamata', 'tooth', 'premi', 'komisi',
        'masabakti', 'thr', 'otherincome', 'othremark', 'rumah', 'jabatan',
        'listrik', 'leave', 'sanksi', 'fixedtax', 'jkm', 'jkk', 'jht',
        'bpjskaryawan', 'bpjsperusahaan', 'refund', 'yayasan', 'personaladvance',
        'koperasi', 'businessadvance', 'loancar', 'loanother', 'credit',
        'milenium', 'grossincome', 'netincomeexpph22', 'taxamonth', 'total', 'thp',
        'users_id'
    ];

    // Relationship ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // Relationship ke Employee (via empno)
    public function employee(): BelongsTo
    {
        return $this->belongsTo(emp_master::class, 'empno', 'empno');
    }

    // Helper untuk format period
    public function getFormattedPeriodAttribute()
    {
        if (strlen($this->period) == 6) {
            $year = substr($this->period, 0, 4);
            $month = substr($this->period, 4, 2);
            return date('F Y', mktime(0, 0, 0, $month, 1, $year));
        }
        return $this->period;
    }
}