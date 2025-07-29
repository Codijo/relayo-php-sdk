<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Relayo\SDK\RelayoSDK;
use Relayo\SDK\Exceptions\ApiException;
use Relayo\SDK\Exceptions\AuthenticationException;

// Configuração do SDK
$relayo = RelayoSDK::create('https://api.relayo.com.br', [
    'timeout' => 30,
    'max_retries' => 3,
    'log_requests' => true
]);

echo "=== Relayo PHP SDK - Exemplo Básico ===\n\n";

// 1. Autenticação
echo "1. Configurando autenticação...\n";
$relayo->setToken('seu_token_bearer_aqui');

echo "✅ Token configurado com sucesso!\n\n";

// 2. Verificar autenticação
echo "2. Verificando autenticação...\n";
if ($relayo->isAuthenticated()) {
    echo "✅ Token válido!\n\n";
} else {
    echo "❌ Token inválido ou não configurado!\n\n";
    exit(1);
}

// 3. Listar instâncias WhatsApp
echo "3. Listando instâncias WhatsApp...\n";
try {
    $instances = $relayo->whatsapp()->list();
    
    if (empty($instances['data'])) {
        echo "ℹ️  Nenhuma instância encontrada.\n\n";
    } else {
        echo "✅ Encontradas " . count($instances['data']) . " instância(s):\n";
        foreach ($instances['data'] as $instance) {
            echo "   - ID: " . $instance['id'] . "\n";
            echo "     Telefone: " . $instance['phone_number'] . "\n";
            echo "     Nome: " . ($instance['name'] ?? 'N/A') . "\n";
            echo "     Status: " . ($instance['status_label'] ?? 'N/A') . "\n";
            echo "\n";
        }
    }
    
} catch (ApiException $e) {
    echo "❌ Erro ao listar instâncias: " . $e->getMessage() . "\n\n";
}

// 4. Criar nova instância (exemplo)
echo "4. Criando nova instância WhatsApp...\n";
try {
    $newInstance = $relayo->whatsapp()->create([
        'phone_number' => '5511999999999'
    ]);
    
    echo "✅ Instância criada com sucesso!\n";
    echo "   ID: " . $newInstance['id'] . "\n";
    echo "   Telefone: " . $newInstance['phone_number'] . "\n\n";
    
    // 5. Obter instância criada
    echo "5. Obtendo detalhes da instância criada...\n";
    $instance = $relayo->whatsapp()->get($newInstance['id']);
    
    echo "✅ Detalhes da instância:\n";
    echo "   ID: " . $instance['id'] . "\n";
    echo "   Telefone: " . $instance['phone_number'] . "\n";
    echo "   Nome: " . ($instance['name'] ?? 'N/A') . "\n";
    echo "   Status: " . ($instance['status_label'] ?? 'N/A') . "\n\n";
    
    // 6. Atualizar instância
    echo "6. Atualizando instância...\n";
    $updatedInstance = $relayo->whatsapp()->update($newInstance['id'], [
        'name' => 'WhatsApp Teste - ' . date('Y-m-d H:i:s')
    ]);
    
    echo "✅ Instância atualizada!\n";
    echo "   Nome: " . $updatedInstance['name'] . "\n\n";
    
    // 7. Excluir instância (limpeza)
    echo "7. Excluindo instância de teste...\n";
    $relayo->whatsapp()->delete($newInstance['id']);
    echo "✅ Instância excluída!\n\n";
    
} catch (ApiException $e) {
    echo "❌ Erro na operação: " . $e->getMessage() . "\n\n";
}

// 8. Buscar por número de telefone
echo "8. Buscando por número de telefone...\n";
try {
    $searchResults = $relayo->whatsapp()->findByPhoneNumber('5511999999999', 5);
    
    if (empty($searchResults['data'])) {
        echo "ℹ️  Nenhuma instância encontrada para este número.\n\n";
    } else {
        echo "✅ Encontradas " . count($searchResults['data']) . " instância(s):\n";
        foreach ($searchResults['data'] as $instance) {
            echo "   - " . $instance['id'] . " (" . $instance['phone_number'] . ")\n";
        }
        echo "\n";
    }
    
} catch (ApiException $e) {
    echo "❌ Erro na busca: " . $e->getMessage() . "\n\n";
}

// 9. Limpar token
echo "9. Removendo token...\n";
$relayo->auth()->clearToken();
echo "✅ Token removido com sucesso!\n\n";

echo "=== Exemplo concluído! ===\n"; 