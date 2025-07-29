<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Relayo\SDK\RelayoSDK;
use Relayo\SDK\Exceptions\ApiException;
use Relayo\SDK\Exceptions\AuthenticationException;
use Relayo\SDK\Exceptions\RateLimitException;

// Configuração do SDK
$relayo = RelayoSDK::create('https://api.relayo.com.br');

echo "=== Relayo PHP SDK - Tratamento de Erros ===\n\n";

// Exemplo 1: Erro de autenticação
echo "1. Testando erro de autenticação...\n";
try {
    $auth = $relayo->auth()->login('email_invalido@test.com', 'senha_errada');
    echo "❌ Login não deveria ter funcionado!\n";
    
} catch (AuthenticationException $e) {
    echo "✅ Erro de autenticação capturado corretamente:\n";
    echo "   Mensagem: " . $e->getMessage() . "\n";
    echo "   Código: " . $e->getCode() . "\n\n";
}

// Exemplo 2: Tentativa de operação sem autenticação
echo "2. Testando operação sem autenticação...\n";
try {
    $instances = $relayo->whatsapp()->list();
    echo "❌ Operação não deveria ter funcionado sem token!\n";
    
} catch (AuthenticationException $e) {
    echo "✅ Erro de autenticação capturado corretamente:\n";
    echo "   Mensagem: " . $e->getMessage() . "\n\n";
}

// Exemplo 3: Erro de API genérico
echo "3. Testando erro genérico da API...\n";
try {
    // Simular um erro 404
    $instance = $relayo->whatsapp()->get('id-inexistente');
    echo "❌ Operação não deveria ter funcionado!\n";
    
} catch (ApiException $e) {
    echo "✅ Erro da API capturado corretamente:\n";
    echo "   Mensagem: " . $e->getMessage() . "\n";
    echo "   Código: " . $e->getCode() . "\n\n";
}

// Exemplo 4: Tratamento de múltiplos tipos de erro
echo "4. Testando tratamento de múltiplos erros...\n";

function executeApiOperation($relayo, $operation) {
    try {
        return $operation();
        
    } catch (AuthenticationException $e) {
        echo "🔐 Erro de autenticação: " . $e->getMessage() . "\n";
        echo "   Ação: Reautenticar usuário\n";
        return null;
        
    } catch (RateLimitException $e) {
        echo "⏱️  Rate limit excedido: " . $e->getMessage() . "\n";
        echo "   Ação: Aguardar e tentar novamente\n";
        return null;
        
    } catch (ApiException $e) {
        echo "🚨 Erro da API: " . $e->getMessage() . "\n";
        echo "   Código: " . $e->getCode() . "\n";
        echo "   Ação: Verificar logs e contatar suporte\n";
        return null;
        
    } catch (\Exception $e) {
        echo "💥 Erro inesperado: " . $e->getMessage() . "\n";
        echo "   Ação: Verificar configuração\n";
        return null;
    }
}

// Testar diferentes operações
echo "   Testando login com credenciais inválidas...\n";
$result = executeApiOperation($relayo, function() use ($relayo) {
    return $relayo->auth()->login('invalid@test.com', 'wrong');
});

echo "   Testando operação sem autenticação...\n";
$result = executeApiOperation($relayo, function() use ($relayo) {
    return $relayo->whatsapp()->list();
});

echo "\n";

// Exemplo 5: Retry automático (simulado)
echo "5. Simulando retry automático...\n";

function retryOperation($operation, $maxRetries = 3) {
    $attempt = 0;
    
    while ($attempt < $maxRetries) {
        try {
            $attempt++;
            echo "   Tentativa {$attempt}...\n";
            
            return $operation();
            
        } catch (RateLimitException $e) {
            if ($attempt >= $maxRetries) {
                echo "   ❌ Número máximo de tentativas excedido\n";
                throw $e;
            }
            
            $delay = $attempt * 2; // Backoff exponencial
            echo "   ⏱️  Rate limit. Aguardando {$delay}s...\n";
            sleep($delay);
            
        } catch (ApiException $e) {
            echo "   ❌ Erro da API: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

try {
    $result = retryOperation(function() use ($relayo) {
        // Simular uma operação que pode falhar
        return $relayo->whatsapp()->list();
    });
    
    echo "   ✅ Operação concluída com sucesso!\n";
    
} catch (Exception $e) {
    echo "   ❌ Operação falhou após todas as tentativas\n";
}

echo "\n=== Exemplo de tratamento de erros concluído! ===\n"; 