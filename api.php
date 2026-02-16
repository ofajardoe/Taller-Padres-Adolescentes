<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Tu API Key (cámbiala por la nueva)
$apiKey = "AIzaSyCf5boCuA9dZn4MC_Ug6IehpfQdwGx8QUI;
$model = "gemini-1.5-flash";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $prompt = $input['prompt'] ?? '';

    if (empty($prompt)) {
        http_response_code(400);
        echo json_encode(['error' => 'Prompt requerido']);
        exit;
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'systemInstruction' => [
            'parts' => [
                ['text' => 'Eres un psicólogo experto en adolescencia y seguridad digital. Responde de forma clara, empática y práctica para padres.']
            ]
        ]
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($payload),
            'timeout' => 30
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Error conectando con Gemini API']);
        exit;
    }

    $data = json_decode($response, true);
    $result = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sin respuesta';

    echo json_encode(['result' => $result]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>
