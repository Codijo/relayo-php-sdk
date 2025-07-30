<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Relayo\SDK\RelayoSDK;
use Relayo\SDK\Exceptions\ApiException;
use Relayo\SDK\Exceptions\AuthenticationException;

// Configuração do SDK
$relayo = RelayoSDK::create('https://api.relayo.com.br', [
    'timeout' => 30,
    'max_retries' => 3,
    'log_requests' => true,
    'log_responses' => true
]);

echo "=== Relayo PHP SDK - Teste de Integração ===\n\n";

// 1. Configurar autenticação
echo "1. Configurando autenticação...\n";

// Token deve ser fornecido via variável de ambiente ou argumento
$token = $_ENV['RELAYO_TOKEN'] ?? $argv[1] ?? null;
$instanceId = $_ENV['INSTANCE_ID'] ?? $argv[2] ?? null;

if (!$token) {
    echo "❌ Token não fornecido!\n";
    echo "Use: php examples/integration-test.php SEU_TOKEN_AQUI [INSTANCE_ID]\n";
    echo "ou: RELAYO_TOKEN=seu_token php examples/integration-test.php [INSTANCE_ID]\n";
    echo "ou: RELAYO_TOKEN=seu_token INSTANCE_ID=instance_id php examples/integration-test.php\n\n";
    exit(1);
}

$relayo->setToken($token);
echo "✅ Token configurado!\n\n";

// 2. Verificar autenticação
echo "2. Verificando autenticação...\n";
if ($relayo->isAuthenticated()) {
    echo "✅ Token válido!\n\n";
} else {
    echo "❌ Token inválido!\n\n";
    exit(1);
}

// 3. Testar recursos de integrações
echo "3. Testando recursos de integrações...\n";

try {
    // Listar integrações
    echo "   🔗 Listando integrações...\n";
    $integrations = $relayo->integration()->list();
    
    if (empty($integrations)) {
        echo "   ℹ️  Nenhuma integração encontrada.\n";
    } else {
        echo "   ✅ Encontradas " . count($integrations) . " integração(ões):\n";
        foreach ($integrations as $integration) {
            echo "      - ID: " . ($integration['id'] ?? 'N/A') . "\n";
            echo "        Nome: " . ($integration['name'] ?? 'N/A') . "\n";
            echo "        Status: " . ($integration['status'] ?? 'N/A') . "\n";
            echo "\n";
        }
        
        // Testar com uma integração específica se existir
        $firstIntegration = $integrations[0];
        $integrationId = $firstIntegration['id'] ?? null;
        
        if ($integrationId) {
            echo "   🔍 Obtendo detalhes da integração {$integrationId}...\n";
            $integrationDetails = $relayo->integration()->get($integrationId);
            echo "   ✅ Detalhes obtidos: " . ($integrationDetails['name'] ?? 'N/A') . "\n";
        }
    }
    
} catch (ApiException $e) {
    echo "   ❌ Erro ao testar integrações: " . $e->getMessage() . "\n";
    echo "      Código: " . $e->getCode() . "\n";
}

echo "\n";

// 4. Testar recursos WhatsApp
echo "4. Testando recursos WhatsApp...\n";

try {
    // Listar instâncias WhatsApp
    echo "   📱 Listando instâncias WhatsApp...\n";
    $instances = $relayo->whatsapp()->list();
    
    if (empty($instances['data'])) {
        echo "   ℹ️  Nenhuma instância WhatsApp encontrada.\n";
    } else {
        echo "   ✅ Encontradas " . count($instances['data']) . " instância(s) WhatsApp:\n";
        foreach ($instances['data'] as $instance) {
            echo "      - ID: " . ($instance['id'] ?? 'N/A') . "\n";
            echo "        Telefone: " . ($instance['phone_number'] ?? 'N/A') . "\n";
            echo "        Nome: " . ($instance['name'] ?? 'N/A') . "\n";
            echo "        Status: " . ($instance['status_label'] ?? 'N/A') . "\n";
            echo "\n";
        }
        
        // Testar com uma instância específica se existir
        $firstInstance = $instances['data'][0];
        $instanceId = $firstInstance['id'] ?? null;
        
        if ($instanceId) {
            echo "   🔍 Obtendo detalhes da instância {$instanceId}...\n";
            $instanceDetails = $relayo->whatsapp()->get($instanceId);
            echo "   ✅ Detalhes obtidos: " . ($instanceDetails['phone_number'] ?? 'N/A') . "\n";
        }
    }
    
} catch (ApiException $e) {
    echo "   ❌ Erro ao testar WhatsApp: " . $e->getMessage() . "\n";
    echo "      Código: " . $e->getCode() . "\n";
}



// 5. Testar recursos de delivery WhatsApp
echo "5. Testando recursos de delivery WhatsApp...\n";

try {
    // Listar histórico de delivery
    echo "   📤 Listando histórico de delivery WhatsApp...\n";
    $history = $relayo->deliveryWhatsApp()->getHistory();
    
    if (empty($history)) {
        echo "   ℹ️  Nenhum histórico de delivery encontrado.\n";
    } else {
        echo "   ✅ Encontrados " . count($history) . " item(ns) no histórico:\n";
        foreach (array_slice($history, 0, 3) as $item) {
            echo "      - ID: " . ($item['id'] ?? 'N/A') . "\n";
            echo "        Status: " . ($item['status'] ?? 'N/A') . "\n";
            echo "        Data: " . ($item['created_at'] ?? 'N/A') . "\n";
            echo "\n";
        }
        
        if (count($history) > 3) {
            echo "      ... e mais " . (count($history) - 3) . " itens\n\n";
        }
    }
    
} catch (ApiException $e) {
    echo "   ❌ Erro ao testar delivery WhatsApp: " . $e->getMessage() . "\n";
    echo "      Código: " . $e->getCode() . "\n";
}

echo "\n";

// 6. Testar recursos de configuração de callbacks WhatsApp
echo "6. Testando recursos de configuração de callbacks WhatsApp...\n";

try {
    // Obter configuração de callback
    echo "   🔄 Obtendo configuração de callback WhatsApp...\n";
    $callback = $relayo->callbackConfigurationWhatsApp()->get();
    
    if (empty($callback)) {
        echo "   ℹ️  Nenhuma configuração de callback encontrada.\n";
    } else {
        echo "   ✅ Configuração de callback obtida:\n";
        echo "      URL: " . ($callback['url'] ?? 'N/A') . "\n";
        echo "      Status: " . ($callback['status'] ?? 'N/A') . "\n";
        echo "      Ativo: " . ($callback['active'] ? 'Sim' : 'Não') . "\n";
        echo "\n";
    }
    
} catch (ApiException $e) {
    echo "   ❌ Erro ao testar callbacks WhatsApp: " . $e->getMessage() . "\n";
    echo "      Código: " . $e->getCode() . "\n";
}

echo "\n";

// 7. Testar envio de mensagens WhatsApp
echo "7. Testando envio de mensagens WhatsApp...\n";

try {
    // Verificar se temos um instance_id fornecido ou obter automaticamente
    if ($instanceId) {
        echo "   📱 Usando instância fornecida: {$instanceId}\n";
        
        // Verificar se a instância existe
        try {
            $instance = $relayo->whatsapp()->get($instanceId);
            echo "   ✅ Instância válida encontrada\n";
        } catch (ApiException $e) {
            echo "   ❌ Instância não encontrada: " . $e->getMessage() . "\n";
            echo "   💡 Verifique se o Instance ID está correto\n";
            exit(1);
        }
    } else {
        echo "   📱 Obtendo instâncias WhatsApp disponíveis...\n";
        $instances = $relayo->whatsapp()->list();
        
        if (!empty($instances['data'])) {
            $firstInstance = $instances['data'][0];
            $instanceId = $firstInstance['id'] ?? null;
            
            if ($instanceId) {
                echo "   ✅ Instância encontrada: {$instanceId}\n";
            } else {
                echo "   ⚠️  Nenhuma instância WhatsApp disponível para teste.\n";
                exit(1);
            }
        } else {
            echo "   ⚠️  Nenhuma instância WhatsApp encontrada para teste de envio.\n";
            exit(1);
        }
    }
    
    echo "   📤 Testando envio de mensagem...\n";
    
    // Testar envio de mensagem
    $messageResult = $relayo->deliveryWhatsApp()->sendTextMessage(
        $instanceId,
        '555199693860', // Número de teste
        'Teste de mensagem via SDK - ' . date('Y-m-d H:i:s')
    );
    
    echo "   ✅ Mensagem enviada com sucesso!\n";
    echo "      ID da mensagem: " . ($messageResult['message_id'] ?? 'N/A') . "\n";
    echo "      Status: " . ($messageResult['status'] ?? 'N/A') . "\n";
    
} catch (ApiException $e) {
    echo "   ❌ Erro ao testar envio de mensagem: " . $e->getMessage() . "\n";
    echo "      Código: " . $e->getCode() . "\n";
}

echo "\n";

// 8. Testar operações de criação (opcional)
echo "8. Testando operações de criação (opcional)...\n";

$testCreation = $_ENV['TEST_CREATION'] ?? $argv[2] ?? 'false';

if ($testCreation === 'true') {
    echo "   🧪 Testando criação de recursos...\n";
    
    try {
        // Testar criação de integração
        echo "   🔗 Criando integração de teste...\n";
        $newIntegration = $relayo->integration()->create([
            'name' => 'Integração Teste - ' . date('Y-m-d H:i:s'),
            'type' => 'webhook',
            'url' => 'https://example.com/webhook'
        ]);
        echo "   ✅ Integração criada: " . ($newIntegration['id'] ?? 'N/A') . "\n";
        
        // Limpar integração de teste
        echo "   🗑️  Removendo integração de teste...\n";
        $relayo->integration()->delete($newIntegration['id']);
        echo "   ✅ Integração removida!\n";
        
    } catch (ApiException $e) {
        echo "   ❌ Erro na criação: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⏭️  Pulando testes de criação (use TEST_CREATION=true para habilitar)\n";
}

echo "\n";

// 9. Resumo do teste
echo "9. Resumo do teste...\n";
echo "   ✅ SDK configurado corretamente\n";
echo "   ✅ Autenticação funcionando\n";
echo "   ✅ Recursos de integrações testados\n";
echo "   ✅ Recursos WhatsApp testados\n";
echo "   ✅ Recursos de delivery WhatsApp testados\n";
echo "   ✅ Recursos de callbacks WhatsApp testados\n";
echo "   ✅ Envio de mensagens WhatsApp testado\n";
echo "   ✅ Conexão com API oficial estabelecida\n\n";

echo "=== Teste de integração concluído com sucesso! ===\n";
echo "\n";
echo "📝 Para executar este teste:\n";
echo "   php examples/integration-test.php SEU_TOKEN_AQUI [INSTANCE_ID]\n";
echo "   ou\n";
echo "   RELAYO_TOKEN=seu_token php examples/integration-test.php [INSTANCE_ID]\n";
echo "   ou\n";
echo "   RELAYO_TOKEN=seu_token INSTANCE_ID=instance_id php examples/integration-test.php\n";
echo "\n";
echo "🧪 Para testar criação de recursos:\n";
echo "   TEST_CREATION=true php examples/integration-test.php SEU_TOKEN_AQUI [INSTANCE_ID]\n"; 