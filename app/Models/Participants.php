<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participants extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantsFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'first_name',
        'email',
        'phone'
    ];
    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'formation_participant')
            ->withTimestamps();
    }
    public function certificats()
    {
        return $this->hasMany(Certificat::class);
    }
}
