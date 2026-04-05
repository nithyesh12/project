<?php
namespace Services;

class PythonApiClient
{
    private $base_url = "http://127.0.0.1:5000";

    /**
     * Call the Python microservice to get crop recommendations
     * 
     * @param float $soil_ph
     * @param float $rainfall
     * @param float $temperature
     * @param float $humidity
     * @param float $nitrogen
     * @param string $state
     * @return array|null
     */
    public function getRecommendations($soil_ph, $rainfall, $temperature, $humidity, $nitrogen, $state, $season = null)
    {
        $endpoint = $this->base_url . "/api/recommend";

        $payload = json_encode([
            "soil_ph" => $soil_ph,
            "rainfall" => $rainfall,
            "temperature" => $temperature,
            "humidity" => $humidity,
            "nitrogen" => $nitrogen,
            "state" => $state,
            "season" => $season
        ]);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );
        // Timeout settings so PHP doesn't hang if Python service is down
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);

        if ($curl_error) {
            return null;
        }

        if ($http_code == 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['status']) && $data['status'] === 'success') {
                return $data['data']; // Returns array of recommended crops and scores
            }
        }

        // Return null if python API is unavailable or returns an error
        return null;
    }
}
?>
