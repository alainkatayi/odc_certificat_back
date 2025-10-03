<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificat extends Model
{
    /** @use HasFactory<\Database\Factories\CertificatFactory> */
    use HasFactory;
    protected $fillable = [
        'participant_name',
        'participant_email',
        'certificat_file',
    ];
}
