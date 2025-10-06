<?php

namespace App\Http\Controllers;

use App\Models\Participants;
use App\Http\Requests\StoreParticipantsRequest;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Models\Formation;

class ParticipantsController extends Controller
{

    public function index()
    {
        $participants = Participants::all();
        return response()->json([
            "Participants" => $participants
        ]);
    }

    public function getParticipantsByFormation($formationId){
        $formation = Formation::find($formationId);
        $participants = $formation->participants;
        return response()->json([
            "Participants"=> $participants
        ]);
    }

}
