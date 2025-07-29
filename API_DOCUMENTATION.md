# Documentação da API Relayo

## Visão Geral

Esta documentação fornece todas as informações necessárias para o desenvolvimento de um SDK que se comunique com a API Relayo. A API é baseada em Laravel 11 com autenticação via Laravel Passport.

## Informações Técnicas

- **Framework**: Laravel 11
- **Autenticação**: Laravel Passport (OAuth2)
- **Versão PHP**: >=8.2

## Estrutura da API

A API está organizada em três áreas principais:

1. **Panel** - Área do cliente/painel
2. **Collective** - Área coletiva/pública
3. **Manager** - Área de gerenciamento

## Autenticação

### Tipos de Autenticação

1. **API Customer** (`auth:api`) - Para usuários do painel
2. **Manager API** (`auth:manager-api`) - Para usuários do gerenciamento

### Headers Necessários

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

## Endpoints da API

### 1. Área Panel (Cliente)

#### Autenticação

##### Login
- **URL**: `POST /panel/customer/login`
- **Autenticação**: Não requerida
- **Parâmetros**:
  ```json
  {
    "email": "string",
    "password": "string"
  }
  ```
- **Resposta de Sucesso** (200):
  ```json
  {
    "success": {
      "token": "string"
    },
    "data": {
      "id": "integer",
      "email": "string",
      "customer_id": "integer",
      "api_token": "string"
    }
  }
  ```
- **Resposta de Erro** (401):
  ```json
  {
    "errors": ["E-mail ou Senha são inválidos. Caso não lembre da sua senha, clique em \"Esqueci minha senha\"."],
    "data": []
  }
  ```

##### Logout
- **URL**: `POST /panel/customer/logout`
- **Autenticação**: Bearer Token
- **Resposta** (200):
  ```json
  {
    "data": []
  }
  ```

#### WhatsApp

##### Listar Instâncias WhatsApp
- **URL**: `GET /panel/application/server/instance/whatsapp`
- **Autenticação**: Bearer Token
- **Parâmetros de Filtro**:
  - `phone_number` (string)
  - `per_page` (integer, padrão: 10)
- **Resposta** (200):
  ```json
  {
    "data": {
      "current_page": "integer",
      "data": [
        {
          "id": "uuid",
          "phone_number": "string",
          "name": "string",
          "status_label": "string",
          "status_color": "string"
        }
      ],
      "per_page": "integer",
      "total": "integer"
    }
  }
  ```

##### Criar Instância WhatsApp
- **URL**: `POST /panel/application/server/instance/whatsapp`
- **Autenticação**: Bearer Token
- **Parâmetros**:
  ```json
  {
    "phone_number": "string"
  }
  ```
- **Resposta** (201):
  ```json
  {
    "data": {
      "id": "uuid",
      "phone_number": "string"
    }
  }
  ```

##### Obter Instância WhatsApp
- **URL**: `GET /panel/application/server/instance/whatsapp/{id}`
- **Autenticação**: Bearer Token
- **Parâmetros**: `id` (uuid)
- **Resposta** (200):
  ```json
  {
    "data": {
      "id": "uuid",
      "phone_number": "string"
    }
  }
  ```

##### Atualizar Instância WhatsApp
- **URL**: `PUT /panel/application/server/instance/whatsapp/{id}`
- **Autenticação**: Bearer Token
- **Parâmetros**: `id` (uuid) + campos da instância
- **Resposta** (200):
  ```json
  {
    "data": {
      "id": "uuid",
      "phone_number": "string"
    }
  }
  ```

##### Excluir Instância WhatsApp
- **URL**: `DELETE /panel/application/server/instance/whatsapp/{id}`
- **Autenticação**: Bearer Token
- **Parâmetros**: `id` (uuid)
- **Resposta** (200):
  ```json
  {
    "data": []
  }
  ```


## Códigos de Status HTTP

- **200**: Sucesso
- **201**: Criado com sucesso
- **401**: Não autorizado
- **404**: Não encontrado
- **405**: Método não permitido
- **422**: Erro de validação
- **500**: Erro interno do servidor

## Padrões de Resposta

### Resposta de Sucesso
```json
{
  "data": {
    // dados da resposta
  }
}
```

### Resposta de Erro
```json
{
  "errors": [
    "Mensagem de erro"
  ],
  "data": []
}
```

### Resposta de Validação
```json
{
  "message": "Erro de validação.",
  "errors": {
    "campo": [
      "Mensagem de erro"
    ]
  }
}
```

## Middleware

### CORS
Todas as rotas utilizam o middleware `cors` para permitir requisições cross-origin.

### Autenticação
- `auth:api` - Para usuários do painel
- `auth:manager-api` - Para usuários do gerenciamento

### Verificação de Cliente
O middleware `CheckCustomer` verifica:
- Se o usuário possui cobranças em atraso
- Se o status do cliente permite acesso (não bloqueado)

## Padrões de ID

- **Usuários e Clientes**: Integer
- **Pedidos e Instâncias WhatsApp**: UUID (formato: `[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}`)
- **Empresas**: UUID (formato: `[0-9a-f]{24}`)

## Paginação

Endpoints que retornam listas suportam paginação com os parâmetros:
- `per_page` (integer, padrão: 10)
- `page` (integer, padrão: 1)

Resposta paginada:
```json
{
  "data": {
    "current_page": 1,
    "data": [...],
    "per_page": 10,
    "total": 100
  }
}
```

## Filtros

Endpoints de listagem suportam filtros via query parameters:
- **Filtros exatos**: `?campo=valor`
- **Filtros LIKE**: `?campo=valor` (busca parcial)

## Considerações para o SDK

1. **Autenticação**: Implementar refresh token automático
2. **Rate Limiting**: Implementar retry com backoff exponencial
3. **Validação**: Validar dados antes do envio
4. **Cache**: Implementar cache para dados que não mudam frequentemente
5. **Logs**: Implementar logs detalhados para debug
6. **Tratamento de Erros**: Mapear todos os códigos de erro possíveis
7. **Upload**: Implementar upload progress para arquivos grandes
8. **Paginação**: Implementar paginação automática ou manual
9. **Filtros**: Implementar builders para facilitar filtros
10. **Tipagem**: Usar tipagem forte para melhor experiência do desenvolvedor

## Exemplo de Uso do SDK

```php
// Exemplo conceitual
$relayo = new RelayoSDK([
    'base_url' => 'https://api.relayo.com.br',
    'timeout' => 30
]);

// Login
$auth = $relayo->auth()->login('email@exemplo.com', 'senha');
$token = $auth['success']['token'];

// Configurar token
$relayo->setToken($token);

// Listar instâncias WhatsApp
$whatsapps = $relayo->whatsapp()->list();

// Criar nova instância
$newWhatsapp = $relayo->whatsapp()->create([
    'phone_number' => '5511999999999'
]);

// Upload avatar
$avatar = $relayo->user()->uploadAvatar($userId, $filePath);
```

Esta documentação fornece todas as informações necessárias para desenvolver um SDK completo e robusto para a API Relayo. 