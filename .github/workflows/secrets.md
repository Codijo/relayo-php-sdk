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

## Sistema de Release Automático

O SDK agora possui **release automático** baseado em **Conventional Commits**:

### Como Funciona

1. **Push para main**: Qualquer push para a branch main dispara o workflow
2. **Análise de commits**: O sistema analisa os commits desde o último release
3. **Versionamento automático**:
   - `feat:` → **minor** version (1.0.0 → 1.1.0)
   - `fix:` → **patch** version (1.0.0 → 1.0.1)
   - `BREAKING CHANGE` → **major** version (1.0.0 → 2.0.0)
4. **Release automático**: Cria tag, release no GitHub e atualiza Packagist

### Exemplos de Commits

```bash
# Patch release (1.0.0 → 1.0.1)
git commit -m "fix: corrigir erro de autenticação"

# Minor release (1.0.0 → 1.1.0)
git commit -m "feat: adicionar suporte a webhooks"

# Major release (1.0.0 → 2.0.0)
git commit -m "feat: nova API de autenticação

BREAKING CHANGE: método de login alterado"
```

### Release Manual

Para releases manuais, ainda é possível usar tags:

```bash
# Criar tag manual
git tag v1.0.0
git push origin v1.0.0
```

## Fluxo Completo

1. **Desenvolvimento**: Faça commits convencionais
2. **Push**: Push para main dispara análise
3. **Análise**: Sistema verifica commits desde último release
4. **Versionamento**: Calcula nova versão automaticamente
5. **Release**: Cria tag, release e atualiza Packagist
6. **Disponibilização**: Pacote fica disponível no Packagist

## Monitoramento

- **GitHub Actions**: https://github.com/Codijo/relayo-php-sdk/actions
- **Packagist**: https://packagist.org/packages/codijo/relayo-php-sdk
- **Releases**: https://github.com/Codijo/relayo-php-sdk/releases

## Vantagens

✅ **Totalmente automático** - Sem intervenção manual  
✅ **Versionamento inteligente** - Baseado no tipo de mudança  
✅ **Conventional Commits** - Padrão da comunidade  
✅ **CI/CD completo** - Testes, análise e release  
✅ **Packagist automático** - Atualização instantânea 