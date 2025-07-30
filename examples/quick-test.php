<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Relayo\SDK\RelayoSDK;
use Relayo\SDK\Exceptions\ApiException;

echo "=== Relayo PHP SDK - Teste Rápido ===\n\n";

// Verificar se token foi fornecido
$token = $argv[1] ?? null;

if (!$token) {
    echo "❌ Token não fornecido!\n";
    echo "Use: php examples/quick-test.php SEU_TOKEN_AQUI\n\n";
    exit(1);
}

// Configurar SDK
$relayo = RelayoSDK::create('https://api.relayo.com.br');
$relayo->setToken($token);

echo "🔧 SDK configurado\n";
echo "🔑 Token configurado\n\n";

// Teste básico de conectividade
try {
    echo "📡 Testando conectividade...\n";
    
    // Tentar listar aplicações
    $applications = $relayo->application()->list();
    echo "✅ Conectividade OK - " . count($applications) . " aplicação(ões) encontrada(s)\n";
    
    // Tentar listar WhatsApp
    $whatsapps = $relayo->whatsapp()->list();
    echo "✅ WhatsApp OK - " . count($whatsapps['data'] ?? []) . " instância(s) encontrada(s)\n";
    
    echo "\n🎉 SDK funcionando perfeitamente!\n";
    
} catch (ApiException $e) {
    echo "❌ Erro na API: " . $e->getMessage() . "\n";
    echo "   Código: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
} 