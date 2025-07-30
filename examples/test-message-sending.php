<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Relayo\SDK\RelayoSDK;
use Relayo\SDK\Exceptions\ApiException;

echo "=== Relayo PHP SDK - Teste de Envio de Mensagens ===\n\n";

// Verificar se o token foi fornecido
$token = $_ENV['RELAYO_TOKEN'] ?? $argv[1] ?? null;
$instanceId = $_ENV['INSTANCE_ID'] ?? $argv[2] ?? null;

if (!$token) {
    echo "❌ Token não fornecido!\n";
    echo "Use: php examples/test-message-sending.php SEU_TOKEN_AQUI [INSTANCE_ID]\n";
    echo "ou: RELAYO_TOKEN=seu_token php examples/test-message-sending.php [INSTANCE_ID]\n";
    echo "ou: RELAYO_TOKEN=seu_token INSTANCE_ID=instance_id php examples/test-message-sending.php\n\n";
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
echo "🔑 Token configurado\n";
if ($instanceId) {
    echo "📱 Instance ID fornecido: {$instanceId}\n";
} else {
    echo "📱 Instance ID não fornecido - será obtido automaticamente\n";
}
echo "\n";

try {
    // 1. Verificar autenticação
    echo "1. Verificando autenticação...\n";
    
    if (!$relayo->isAuthenticated()) {
        echo "   ❌ Token inválido ou expirado\n";
        exit(1);
    }
    
    echo "   ✅ Autenticação válida\n\n";
    
    // 2. Verificar/obter instância WhatsApp
    if ($instanceId) {
        echo "2. Verificando instância WhatsApp fornecida...\n";
        
        try {
            $instance = $relayo->whatsapp()->get($instanceId);
            echo "   ✅ Instância encontrada:\n";
            echo "      - ID: " . ($instance['id'] ?? 'N/A') . "\n";
            echo "      - Telefone: " . ($instance['phone_number'] ?? 'N/A') . "\n";
            echo "      - Nome: " . ($instance['name'] ?? 'N/A') . "\n";
            echo "      - Status: " . ($instance['status_label'] ?? 'N/A') . "\n\n";
        } catch (ApiException $e) {
            echo "   ❌ Instância não encontrada: " . $e->getMessage() . "\n";
            echo "   💡 Verifique se o Instance ID está correto\n";
            exit(1);
        }
    } else {
        echo "2. Obtendo instâncias WhatsApp...\n";
        
        $instances = $relayo->whatsapp()->list();
        
        if (empty($instances['data'])) {
            echo "   ❌ Nenhuma instância WhatsApp encontrada\n";
            echo "   💡 Certifique-se de ter instâncias WhatsApp configuradas\n";
            exit(1);
        }
        
        echo "   ✅ Encontradas " . count($instances['data']) . " instância(s) WhatsApp:\n";
        foreach ($instances['data'] as $instance) {
            echo "      - ID: " . ($instance['id'] ?? 'N/A') . "\n";
            echo "        Telefone: " . ($instance['phone_number'] ?? 'N/A') . "\n";
            echo "        Nome: " . ($instance['name'] ?? 'N/A') . "\n";
            echo "        Status: " . ($instance['status_label'] ?? 'N/A') . "\n";
            echo "\n";
        }
        
        $firstInstance = $instances['data'][0];
        $instanceId = $firstInstance['id'] ?? null;
        
        if (!$instanceId) {
            echo "   ❌ ID da instância não encontrado\n";
            exit(1);
        }
    }
    
    // 3. Testar envio de mensagem
    echo "3. Testando envio de mensagem...\n";
    
    echo "   📱 Usando instância: {$instanceId}\n";
    echo "   📤 Enviando mensagem...\n";
    
    // Enviar mensagem de teste
    $messageResult = $relayo->deliveryWhatsApp()->sendTextMessage(
        $instanceId,
        '555199693860', // Número de teste
        '🧪 Teste de mensagem via SDK Relayo - ' . date('Y-m-d H:i:s') . ' |o|'
    );
    
    echo "   ✅ Mensagem enviada com sucesso!\n";
    echo "      ID da mensagem: " . ($messageResult['message_id'] ?? 'N/A') . "\n";
    echo "      Status: " . ($messageResult['status'] ?? 'N/A') . "\n";
    echo "      Sucesso: " . ($messageResult['success'] ? 'Sim' : 'Não') . "\n";
    
    // 4. Testar envio com dados customizados
    echo "\n4. Testando envio com dados customizados...\n";
    
    $customData = [
        'instance_id' => $instanceId,
        'to' => '555199693860',
        'message' => '📱 Teste customizado via SDK - ' . date('Y-m-d H:i:s')
    ];
    
    $customResult = $relayo->deliveryWhatsApp()->sendTextMessageWithData($customData);
    
    echo "   ✅ Mensagem customizada enviada!\n";
    echo "      ID da mensagem: " . ($customResult['message_id'] ?? 'N/A') . "\n";
    echo "      Status: " . ($customResult['status'] ?? 'N/A') . "\n";
    echo "      Sucesso: " . ($customResult['success'] ? 'Sim' : 'Não') . "\n";
    
} catch (ApiException $e) {
    echo "   ❌ Erro da API: " . $e->getMessage() . "\n";
    echo "      Código: " . $e->getCode() . "\n";
} catch (\Exception $e) {
    echo "   ❌ Erro inesperado: " . $e->getMessage() . "\n";
}

echo "\n=== Teste de envio de mensagens concluído! ===\n";
echo "\n📝 Para executar este teste:\n";
echo "   php examples/test-message-sending.php SEU_TOKEN_AQUI [INSTANCE_ID]\n";
echo "   ou\n";
echo "   RELAYO_TOKEN=seu_token php examples/test-message-sending.php [INSTANCE_ID]\n";
echo "   ou\n";
echo "   RELAYO_TOKEN=seu_token INSTANCE_ID=instance_id php examples/test-message-sending.php\n"; 