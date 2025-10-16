<?php

namespace App\Http\Controllers;

use App\Models\Participants;
use App\Http\Requests\StoreParticipantsRequest;
use App\Http\Requests\UpdateParticipantsRequest;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ParticipantsController extends Controller
{

    public function index()
    {
        $participants = Participants::paginate(5);
        return response()->json($participants);
    }

    public function getParticipantsByFormation($formationId)
    {
        $formation = Formation::find($formationId);
        $participants = $formation->participants;
        return response()->json([
            "Participants" => $participants
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:13',
            'email' => 'required|email|max:255',
            'formation_id' => 'required|exists:formations,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $participant = Participants::create([
            'first_name' => $request->first_name,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        $participant->formations()->attach($request->formation_id);

        return response()->json([
            "message" => "Participant créé avec succès et associé à la formation",
            "Participant" => $participant
        ], 201);
    }
}
