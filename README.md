# Relayo PHP SDK

SDK PHP oficial para a API Relayo - Comunicação com WhatsApp e gerenciamento de instâncias.

## 📋 Requisitos

- PHP >= 8.2
- Composer

## 🚀 Instalação

### Via Composer

```bash
composer require codijo/relayo-php-sdk
```

### Instalação Manual

```bash
git clone https://github.com/Codijo/relayo-php-sdk.git
cd relayo-php-sdk
composer install
```

## 📖 Documentação

### Configuração Inicial

```php
<?php

use Relayo\SDK\RelayoSDK;

// Criação básica do SDK
$relayo = RelayoSDK::create('https://api.relayo.com.br');

// Com configurações personalizadas
$relayo = RelayoSDK::create('https://api.relayo.com.br', [
    'timeout' => 30,
    'max_retries' => 3,
    'retry_delay' => 1,
    'exponential_backoff' => true,
    'log_requests' => true,
    'log_responses' => true
]);
```

### Autenticação

```php
<?php

use Relayo\SDK\RelayoSDK;

$relayo = RelayoSDK::create('https://api.relayo.com.br');

// Configurar token de autenticação Bearer
$relayo->setToken('seu_token_bearer_aqui');

// Verificar se está autenticado
if ($relayo->isAuthenticated()) {
    echo "Token configurado com sucesso!\n";
}

// Ou usar o AuthManager diretamente
$relayo->auth()->setToken('seu_token_bearer_aqui');

// Validar token
if ($relayo->auth()->validateToken()) {
    echo "Token válido!\n";
}

// Remover token
$relayo->auth()->clearToken();
```

**Importante:** O token deve ser obtido através do seu sistema de autenticação (Laravel Passport) e fornecido ao SDK.

### Gerenciamento de Recursos

#### Instâncias WhatsApp

```php
<?php

// Listar todas as instâncias
$instances = $relayo->whatsapp()->list();

foreach ($instances['data'] as $instance) {
    echo "ID: " . $instance['id'] . "\n";
    echo "Telefone: " . $instance['phone_number'] . "\n";
    echo "Nome: " . $instance['name'] . "\n";
    echo "Status: " . $instance['status_label'] . "\n";
    echo "---\n";
}

// Com filtros
$instances = $relayo->whatsapp()->list([
    'phone_number' => '5511999999999',
    'per_page' => 5
]);

// Com paginação
$instances = $relayo->whatsapp()->listPaginated(1, 10);
```

#### Aplicações

```php
<?php

// Listar aplicações
$applications = $relayo->application()->list();

// Criar aplicação
$newApp = $relayo->application()->create([
    'name' => 'Minha Aplicação',
    'description' => 'Descrição da aplicação'
]);

// Obter aplicação específica
$app = $relayo->application()->get('app-id');

// Atualizar aplicação
$updatedApp = $relayo->application()->update('app-id', [
    'name' => 'Nome Atualizado'
]);

// Estatísticas da aplicação
$stats = $relayo->application()->getStats('app-id');

// Ativar/Desativar aplicação
$relayo->application()->activate('app-id');
$relayo->application()->deactivate('app-id');
```


#### Criar Instância

```php
<?php

try {
    $newInstance = $relayo->whatsapp()->create([
        'phone_number' => '5511999999999'
    ]);
    
    echo "Instância criada com sucesso!\n";
    echo "ID: " . $newInstance['id'] . "\n";
    echo "Telefone: " . $newInstance['phone_number'] . "\n";
    
} catch (\Relayo\SDK\Exceptions\ApiException $e) {
    echo "Erro ao criar instância: " . $e->getMessage() . "\n";
}
```

#### Obter Instância Específica

```php
<?php

$instanceId = '123e4567-e89b-12d3-a456-426614174000';

try {
    $instance = $relayo->whatsapp()->get($instanceId);
    
    echo "ID: " . $instance['id'] . "\n";
    echo "Telefone: " . $instance['phone_number'] . "\n";
    echo "Nome: " . $instance['name'] . "\n";
    echo "Status: " . $instance['status_label'] . "\n";
    
} catch (\Relayo\SDK\Exceptions\ApiException $e) {
    echo "Erro ao obter instância: " . $e->getMessage() . "\n";
}
```

#### Atualizar Instância

```php
<?php

$instanceId = '123e4567-e89b-12d3-a456-426614174000';

try {
    $updatedInstance = $relayo->whatsapp()->update($instanceId, [
        'name' => 'WhatsApp Atualizado'
    ]);
    
    echo "Instância atualizada com sucesso!\n";
    echo "Nome: " . $updatedInstance['name'] . "\n";
    
} catch (\Relayo\SDK\Exceptions\ApiException $e) {
    echo "Erro ao atualizar instância: " . $e->getMessage() . "\n";
}
```

#### Excluir Instância

```php
<?php

$instanceId = '123e4567-e89b-12d3-a456-426614174000';

try {
    $relayo->whatsapp()->delete($instanceId);
    echo "Instância excluída com sucesso!\n";
    
} catch (\Relayo\SDK\Exceptions\ApiException $e) {
    echo "Erro ao excluir instância: " . $e->getMessage() . "\n";
}
```

#### Buscar por Número de Telefone

```php
<?php

$phoneNumber = '5511999999999';

try {
    $instances = $relayo->whatsapp()->findByPhoneNumber($phoneNumber, 5);
    
    foreach ($instances['data'] as $instance) {
        echo "Encontrada instância: " . $instance['id'] . "\n";
    }
    
} catch (\Relayo\SDK\Exceptions\ApiException $e) {
    echo "Erro na busca: " . $e->getMessage() . "\n";
}
```

### Tratamento de Erros

```php
<?php

use Relayo\SDK\Exceptions\ApiException;
use Relayo\SDK\Exceptions\AuthenticationException;
use Relayo\SDK\Exceptions\RateLimitException;

try {
    $instances = $relayo->whatsapp()->list();
    
} catch (AuthenticationException $e) {
    echo "Erro de autenticação: " . $e->getMessage() . "\n";
    // Reautenticar ou redirecionar para login
    
} catch (RateLimitException $e) {
    echo "Rate limit excedido. Aguarde um momento.\n";
    // Implementar retry com delay
    
} catch (ApiException $e) {
    echo "Erro da API: " . $e->getMessage() . "\n";
    echo "Código: " . $e->getCode() . "\n";
    
} catch (\Exception $e) {
    echo "Erro inesperado: " . $e->getMessage() . "\n";
}
```

### Configuração Avançada

```php
<?php

use Relayo\SDK\RelayoSDK;
use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Cliente HTTP personalizado
$httpClient = new Client([
    'timeout' => 60,
    'verify' => false // Para desenvolvimento
]);

// Logger personalizado
$logger = new Logger('relayo-sdk');
$logger->pushHandler(new StreamHandler('relayo.log', Logger::DEBUG));

// SDK com dependências personalizadas
$relayo = new RelayoSDK(
    new \Relayo\SDK\Config('https://api.relayo.com.br', [
        'timeout' => 60,
        'max_retries' => 5,
        'log_requests' => true
    ]),
    $httpClient,
    null,
    null,
    $logger
);
```

## 🧪 Testes

### Executar Testes

```bash
# Executar todos os testes
composer test

# Com cobertura de código
composer test-coverage

# Testes específicos
./vendor/bin/phpunit tests/RelayoSDKTest.php
```

### Estrutura de Testes

```
tests/
├── RelayoSDKTest.php           # Testes principais do SDK
├── Auth/
│   └── AuthManagerTest.php     # Testes de autenticação
└── Resources/
    ├── WhatsAppResourceTest.php # Testes do recurso WhatsApp
    ├── ApplicationResourceTest.php # Testes do recurso Aplicações
```

## 📁 Estrutura do Projeto

```
src/
├── RelayoSDK.php               # Classe principal do SDK
├── Config.php                  # Configuração
├── Auth/
│   └── AuthManager.php         # Gerenciador de autenticação
├── Http/
│   └── HttpClient.php          # Cliente HTTP PSR-18
├── Resources/
│   ├── WhatsAppResource.php    # Recurso WhatsApp
│   ├── ApplicationResource.php # Recurso Aplicações
└── Exceptions/
    ├── ApiException.php        # Exceção base
    ├── AuthenticationException.php
    └── RateLimitException.php
```

## 🔧 Configurações

### Opções de Configuração

| Opção | Tipo | Padrão | Descrição |
|-------|------|--------|-----------|
| `timeout` | int | 30 | Timeout das requisições em segundos |
| `max_retries` | int | 3 | Número máximo de tentativas |
| `retry_delay` | int | 1 | Delay entre tentativas em segundos |
| `exponential_backoff` | bool | true | Usar backoff exponencial |
| `log_requests` | bool | false | Logar requisições |
| `log_responses` | bool | false | Logar respostas |

### Headers Automáticos

O SDK adiciona automaticamente os seguintes headers:

- `Accept: application/json`
- `Content-Type: application/json` (para POST/PUT)
- `Authorization: Bearer {token}` (quando autenticado)
- `User-Agent: Relayo-PHP-SDK/1.0`

## 🚀 Funcionalidades

### ✅ Implementadas

- ✅ Autenticação via token
- ✅ Operações CRUD para WhatsApp
- ✅ Retry automático com backoff exponencial
- ✅ Tratamento de erros específicos
- ✅ Logging configurável
- ✅ Paginação
- ✅ Filtros
- ✅ Testes automatizados
- ✅ PSR-4 Autoloading
- ✅ PSR-18 HTTP Client
- ✅ PSR-3 Logging

### 🔄 Recursos Futuros

- 🔄 Upload de arquivos
- 🔄 Webhooks
- 🔄 Cache automático
- 🔄 Refresh token automático
- 🔄 Mais recursos da API

## 📝 Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📞 Suporte

- **Email**: dev@codijo.com.br
- **Documentação**: [docs.relayo.com.br](https://docs.relayo.com.br)
- **Issues**: [GitHub Issues](https://github.com/Codijo/relayo-php-sdk/issues)

## 🔗 Links Úteis

- [API Documentation](API_DOCUMENTATION.md)
- [Packagist](https://packagist.org/packages/codijo/relayo-php-sdk)
- [GitHub Repository](https://github.com/Codijo/relayo-php-sdk) 