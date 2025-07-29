# GitHub Actions Secrets

Para que o CI/CD funcione corretamente, você precisa configurar os seguintes secrets no repositório GitHub:

## Secrets Necessários

### 1. PACKAGIST_USERNAME
- **Descrição**: Seu nome de usuário no Packagist
- **Como obter**: Acesse [packagist.org](https://packagist.org) e veja seu nome de usuário no perfil
- **Exemplo**: `codijo`

### 2. PACKAGIST_TOKEN
- **Descrição**: Token de API do Packagist para atualizar o pacote
- **Como obter**:
  1. Acesse [packagist.org](https://packagist.org)
  2. Vá em "Profile" → "API Tokens"
  3. Clique em "Generate Token"
  4. Copie o token gerado
- **Exemplo**: `abc123def456ghi789`

## Como Configurar

1. Acesse o repositório no GitHub: https://github.com/Codijo/relayo-php-sdk
2. Vá em "Settings" → "Secrets and variables" → "Actions"
3. Clique em "New repository secret"
4. Adicione cada secret com o nome e valor correspondente

## Verificação

Após configurar os secrets, você pode testar criando um release:

```bash
# Criar um release patch (1.0.0 -> 1.0.1)
./scripts/release.sh patch

# Criar um release minor (1.0.0 -> 1.1.0)
./scripts/release.sh minor

# Criar um release major (1.0.0 -> 2.0.0)
./scripts/release.sh major
```

## Fluxo de Release

1. **Script de Release**: Executa testes e cria tag
2. **GitHub Actions**: Detecta a tag e executa o workflow
3. **Testes**: Executa todos os testes e verificações
4. **Release**: Cria release no GitHub
5. **Packagist**: Notifica o Packagist para atualizar o pacote

## Monitoramento

- **GitHub Actions**: https://github.com/Codijo/relayo-php-sdk/actions
- **Packagist**: https://packagist.org/packages/codijo/relayo-php-sdk
- **Releases**: https://github.com/Codijo/relayo-php-sdk/releases 