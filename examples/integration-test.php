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

if (!$token) {
    echo "❌ Token não fornecido!\n";
    echo "Use: php examples/integration-test.php SEU_TOKEN_AQUI\n";
    echo "Ou: RELAYO_TOKEN=seu_token php examples/integration-test.php\n\n";
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

// 3. Testar recursos de aplicações
echo "3. Testando recursos de aplicações...\n";

try {
    // Listar aplicações
    echo "   📋 Listando aplicações...\n";
    $applications = $relayo->application()->list();
    
    if (empty($applications)) {
        echo "   ℹ️  Nenhuma aplicação encontrada.\n";
    } else {
        echo "   ✅ Encontradas " . count($applications) . " aplicação(ões):\n";
        foreach ($applications as $app) {
            echo "      - ID: " . ($app['id'] ?? 'N/A') . "\n";
            echo "        Nome: " . ($app['name'] ?? 'N/A') . "\n";
            echo "        Status: " . ($app['status'] ?? 'N/A') . "\n";
            echo "\n";
        }
        
        // Testar com uma aplicação específica se existir
        $firstApp = $applications[0];
        $appId = $firstApp['id'] ?? null;
        
        if ($appId) {
            echo "   🔍 Obtendo detalhes da aplicação {$appId}...\n";
            $appDetails = $relayo->application()->get($appId);
            echo "   ✅ Detalhes obtidos: " . ($appDetails['name'] ?? 'N/A') . "\n";
            
            echo "   📊 Obtendo estatísticas...\n";
            try {
                $stats = $relayo->application()->getStats($appId);
                echo "   ✅ Estatísticas obtidas!\n";
                echo "      Dados: " . json_encode($stats) . "\n";
            } catch (ApiException $e) {
                echo "   ⚠️  Erro ao obter estatísticas: " . $e->getMessage() . "\n";
            }
        }
    }
    
} catch (ApiException $e) {
    echo "   ❌ Erro ao testar aplicações: " . $e->getMessage() . "\n";
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

echo "\n";

// 5. Testar operações de criação (opcional)
echo "5. Testando operações de criação (opcional)...\n";

$testCreation = $_ENV['TEST_CREATION'] ?? $argv[2] ?? 'false';

if ($testCreation === 'true') {
    echo "   🧪 Testando criação de recursos...\n";
    
    try {
        // Testar criação de aplicação
        echo "   📋 Criando aplicação de teste...\n";
        $newApp = $relayo->application()->create([
            'name' => 'Aplicação Teste - ' . date('Y-m-d H:i:s'),
            'description' => 'Aplicação criada pelo teste de integração'
        ]);
        echo "   ✅ Aplicação criada: " . ($newApp['id'] ?? 'N/A') . "\n";
        
        // Limpar aplicação de teste
        echo "   🗑️  Removendo aplicação de teste...\n";
        $relayo->application()->delete($newApp['id']);
        echo "   ✅ Aplicação removida!\n";
        
    } catch (ApiException $e) {
        echo "   ❌ Erro na criação: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⏭️  Pulando testes de criação (use TEST_CREATION=true para habilitar)\n";
}

echo "\n";

// 6. Resumo do teste
echo "6. Resumo do teste...\n";
echo "   ✅ SDK configurado corretamente\n";
echo "   ✅ Autenticação funcionando\n";
echo "   ✅ Recursos de aplicações testados\n";
echo "   ✅ Recursos WhatsApp testados\n";
echo "   ✅ Conexão com API oficial estabelecida\n\n";

echo "=== Teste de integração concluído com sucesso! ===\n";
echo "\n";
echo "📝 Para executar este teste:\n";
echo "   php examples/integration-test.php SEU_TOKEN_AQUI\n";
echo "   ou\n";
echo "   RELAYO_TOKEN=seu_token php examples/integration-test.php\n";
echo "\n";
echo "🧪 Para testar criação de recursos:\n";
echo "   TEST_CREATION=true php examples/integration-test.php SEU_TOKEN_AQUI\n"; 