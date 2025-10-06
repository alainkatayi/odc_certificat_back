<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;

class Certificat extends Model
{
    /** @use HasFactory<\Database\Factories\CertificatFactory> */
    use HasFactory;
    protected $fillable = [
        'participants_id',
        'formation_id',
        'certificat_path',
    ];

    public function participant()
    {
        return $this->belongsTo(Participants::class);
    }
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
