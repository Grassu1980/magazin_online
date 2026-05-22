<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ANAFService
{
    protected string $apiUrl = 'https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva';

    /**
     * Caută informații despre un furnizor după CUI în baza de date ANAF
     */
    public function searchByCui(string $cui): ?array
    {
        try {
            $data = now()->format('Y-m-d');
            
            Log::info('ANAF Request: CUI=' . $cui . ', Data=' . $data);
            
            // ANAF API așteaptă un array direct, nu un obiect cu cheia "cui"
            $response = Http::post($this->apiUrl, [
                [
                    'cui' => $cui,
                    'data' => $data
                ]
            ]);

            Log::info('ANAF Response Status: ' . $response->status());
            Log::info('ANAF Response Body: ' . $response->body());

            if ($response->failed()) {
                Log::error('ANAF API Error: ' . $response->body());
                return null;
            }

            $result = $response->json();

            Log::info('ANAF Response JSON: ' . json_encode($result));

            // Verificăm dacă există date în răspuns
            if (empty($result['found']) || empty($result['found'][0])) {
                Log::warning('ANAF: No data found for CUI ' . $cui);
                return null;
            }

            $companyData = $result['found'][0];
            $generalData = $companyData['date_generale'] ?? [];
            $tvaData = $companyData['inregistrare_scop_Tva'] ?? [];

            // Determină datele TVA
            $tvaStatus = 'Neînregistrat';
            $tvaValidFrom = null;
            $tvaValidTo = null;

            if (!empty($tvaData['scpTVA']) && $tvaData['scpTVA'] === true) {
                $tvaStatus = 'Înregistrat';
            } elseif (!empty($tvaData['perioade_TVA']) && is_array($tvaData['perioade_TVA'])) {
                $lastPeriod = end($tvaData['perioade_TVA']);
                if (!empty($lastPeriod['data_sfarsit_ScpTVA'])) {
                    $tvaStatus = 'Anulat';
                    $tvaValidFrom = $lastPeriod['data_inceput_ScpTVA'] ?? null;
                    $tvaValidTo = $lastPeriod['data_sfarsit_ScpTVA'] ?? null;
                }
            }

            return [
                'name' => $generalData['denumire'] ?? null,
                'address' => $generalData['adresa'] ?? null,
                'cui' => $generalData['cui'] ?? null,
                'tva_status' => $tvaStatus,
                'tva_code' => $tvaData['scpTVA'] ?? null,
                'tva_valid_from' => $tvaValidFrom,
                'tva_valid_to' => $tvaValidTo,
                'reg_com' => $generalData['nrRegCom'] ?? null,
                'phone' => $generalData['telefon'] ?? null,
                'email' => null, // ANAF nu returnează email
            ];

        } catch (\Exception $e) {
            Log::error('ANAF Service Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Determină statusul TVA pe baza datelor primite de la ANAF
     */
    private function getTvaStatus(array $companyData): string
    {
        if (!empty($companyData['scpTVA'])) {
            return 'Înregistrat';
        }

        if (!empty($companyData['data_sfarsit_ScpTVA'])) {
            return 'Anulat';
        }

        return 'Neînregistrat';
    }
}
