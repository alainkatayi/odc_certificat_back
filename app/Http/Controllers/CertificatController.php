<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Http\Requests\StoreCertificatRequest;
use App\Http\Requests\UpdateCertificatRequest;
use App\Models\Formation;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CertificatController extends Controller
{

    public function generateCertificates($formationId)
    {
        //on verifie que l'utilisateur est authentifie
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $formation = Formation::find($formationId);
        if (!$formation) {
            return response()->json(['message' => "La formation n'existe pas"], 404);
        }

        $participants = $formation->participants;
        if ($participants->isEmpty()) {
            return response()->json(['message' => 'Aucun participant trouvé pour cette formation'], 404);
        }

        try {
            // on creer le dossier pour stocker les certificats
            Storage::disk('public')->makeDirectory('certificats_generated');

            // chemin du template(le model de certificat)
            $templateRelative = $formation->certificat_file;
            $templatePath = storage_path('app/public/' . $templateRelative);

            //on genere un certificat pour chaque participant
            foreach ($participants as $participant) {

                $pdf = new Fpdi();

                // ouvrir le template
                $pageCount = $pdf->setSourceFile($templatePath);
                $tplIdx = $pdf->importPage(1);
                $size = $pdf->getTemplateSize($tplIdx);

                //ajouter une page (avec meme mesure que le template)
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx);

                //police et taille du texte
                $pdf->SetFont('Helvetica', '', 50);
                $pdf->SetTextColor(0, 0, 0); // Couleur noire

                // position du texte
                $pdf->SetXY(80, 100); //
                $pdf->Write(0, $participant->first_name . ' ' . $participant->name);

                // Ajouter la formation
                $pdf->SetFont('Helvetica', 'I', 20);
                $pdf->SetXY(80, 150);
                $pdf->Write(0, $formation->name);

                //sauvarder le PDF avec le nom du participant et de la formation
                $outputPath = storage_path('app/public/certificats_generated/certificat_' . $formation->name . '_' . $participant->name . '.pdf');
                $pdf->Output('F', $outputPath);

                // Enregistrer le certificat de chaque participant dans la base de données
                Certificat::create([
                    'participant_name' => $participant->name,
                    'participant_email' => $participant->email,
                    'certificat_file' => 'certificats_generated/' . $outputPath,
                ]);
            }
            return response()->json(['message' => 'Certificats générés avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la génération des certificats', 'error' => $e->getMessage()], 500);
        }
    }
}
