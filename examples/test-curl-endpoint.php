<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Relayo\SDK\RelayoSDK;
use Relayo\SDK\Exceptions\ApiException;

echo "=== Relayo PHP SDK - Teste do Endpoint cURL ===\n\n";

// Verificar se o token foi fornecido
$token = $_ENV['RELAYO_TOKEN'] ?? $argv[1] ?? null;

if (!$token) {
    echo "❌ Token não fornecido!\n";
    echo "Use: php examples/test-curl-endpoint.php SEU_TOKEN_AQUI\n\n";
    exit(1);
}

// Configurar SDK
$relayo = RelayoSDK::create('https://api.relayo.com.br', [
    'timeout' => 30,
    'max_retries' => 3
]);

// Configurar token
$relayo->setToken($token);

echo "🔧 SDK configurado\n";
echo "🔑 Token configurado\n\n";

// Testar o endpoint exato que está funcionando no cURL
echo "📡 Testando endpoint de envio de mensagens...\n";

try {
    // Usar o instance_id que está funcionando no cURL
    $instanceId = 'inst_uHnTuxOWxzlop4ETJV5AMzCOIwhXiqlmkmcgP77i';
    
    $data = [
        'instance_id' => $instanceId,
        'to' => '555199693860',
        'message' => 'Aqui, iPORTO DEV!!!! |o|'
    ];
    
    echo "   📤 Enviando dados: " . json_encode($data) . "\n";
    
    // Fazer a requisição diretamente usando o HttpClient
    $response = $relayo->getHttpClient()->post('api/panel/application/delivery/whatsapp/queue/api/delivery/text', $data);
    
    echo "   ✅ Resposta recebida!\n";
    echo "   📊 Status Code: " . $response->getStatusCode() . "\n";
    
    $responseData = json_decode((string) $response->getBody(), true);
    echo "   📄 Resposta: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    
} catch (ApiException $e) {
    echo "   ❌ Erro da API: " . $e->getMessage() . "\n";
    echo "      Código: " . $e->getCode() . "\n";
} catch (\Exception $e) {
    echo "   ❌ Erro inesperado: " . $e->getMessage() . "\n";
}

echo "\n=== Teste concluído! ===\n"; 