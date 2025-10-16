<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Formation;
use App\Models\Participants;
use Illuminate\Support\Facades\Validator;


class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::paginate(5);
        return response()->json($formations);
    }

    public function store(Request $request)
    {
        //on verifie que l'utilisateur est authentifie
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'certificat_file' => 'required|file|mimes:pdf,doc,docx',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'participant_file' => 'required|file|mimes:csv,txt',
        ]);

        // cas d'erreur lors de la  validation
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            // Créer la formation
            $formation = Formation::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            // Sauvegarde du fichier de certificat
            if ($request->hasFile('certificat_file')) {
                $certificatPath = $request->file('certificat_file')->store('certificats', 'public');
                $formation->certificat_file = $certificatPath;
            }

            // Traitement du fichier CSV des participants
            if ($request->hasFile('participant_file')) {

                // Stocker le CSV
                $csvPath = $request->file('participant_file')->store('csvs', 'public');
                $formation->participant_file = $csvPath;

                // Déterminer le séparateur automatiquement (',' ou ';')
                $firstLine = fgets(fopen(storage_path("app/public/" . $csvPath), 'r'));
                $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

                // Ouvrir le CSV
                if (($handle = fopen(storage_path("app/public/" . $csvPath), "r")) !== FALSE) {

                    // Sauter la première ligne (en-tête)
                    fgetcsv($handle, 1000, $delimiter);

                    while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                        // Ignorer les lignes vides
                        if (empty(array_filter($data))) continue;

                        // Supprimer BOM si présent
                        $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);

                        // S'assurer que toutes les colonnes existent
                        $data = array_pad($data, 4, '');

                        // Créer le participant
                        $participant = new Participants();
                        $participant->name = $data[0];
                        $participant->first_name = $data[1];
                        $participant->email = $data[2];
                        $participant->phone = $data[3];
                        $participant->save();

                        // Lier à la formation
                        $formation->participants()->syncWithoutDetaching($participant->id);
                    }

                    //on ferme le fichier
                    fclose($handle);
                }

                // Sauvegarder la formation
                $formation->save();
            }

            $formation->save();
            return response()->json(['message' => 'Formation created successfully', 'formation' => $formation], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }
    }

    public function show($formationId){
        $formation = Formation::findOrFail($formationId);
        return response()->json(["Formation"=>$formation]);
    }
}
