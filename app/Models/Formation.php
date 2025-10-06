<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Participants;
use App\Models\Certificat;

class Formation extends Model
{

    use  HasFactory;
    //use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'certificat_file',
        'participant_file',
        'start_date',
        'end_date'
    ];

    public function participants()
    {
        return $this->belongsToMany(Participants::class, 'formation_participant')
            ->withTimestamps();
    }

    public function certificats()
    {
        return $this->hasMany(Certificat::class);
    }
}
