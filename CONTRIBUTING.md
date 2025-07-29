# Guia de Contribuição

Obrigado por considerar contribuir com o Relayo PHP SDK! Este documento fornece diretrizes para contribuições.

## 📋 Como Contribuir

### 1. Configuração do Ambiente

```bash
# Clone o repositório
git clone https://github.com/Codijo/relayo-php-sdk.git
cd relayo-php-sdk

# Instale as dependências
composer install

# Instale as dependências de desenvolvimento
composer install --dev
```

### 2. Padrões de Código

- Siga o padrão PSR-12
- Use tipagem forte (PHP 8.2+)
- Documente todas as classes e métodos públicos
- Mantenha a cobertura de testes acima de 80%

### 3. Fluxo de Trabalho

1. **Crie uma branch** para sua feature:
   ```bash
   git checkout -b feature/nova-funcionalidade
   ```

2. **Faça suas alterações** seguindo os padrões

3. **Execute os testes**:
   ```bash
   composer test
   composer test-coverage
   ```

4. **Execute a análise estática**:
   ```bash
   composer phpstan
   composer cs-check
   ```

5. **Commit suas mudanças**:
   ```bash
   git add .
   git commit -m "feat: adiciona nova funcionalidade"
   ```

6. **Push para o repositório**:
   ```bash
   git push origin feature/nova-funcionalidade
   ```

7. **Abra um Pull Request**

### 4. Convenções de Commit

Use o padrão [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação de código
- `refactor:` Refatoração
- `test:` Testes
- `chore:` Tarefas de manutenção

### 5. Testes

#### Executar Testes

```bash
# Todos os testes
composer test

# Com cobertura
composer test-coverage

# Testes específicos
./vendor/bin/phpunit tests/RelayoSDKTest.php
```

#### Escrever Testes

- Teste todas as funcionalidades públicas
- Use mocks para dependências externas
- Mantenha os testes simples e focados
- Use nomes descritivos para os métodos de teste

### 6. Análise de Código

#### PHPStan

```bash
composer phpstan
```

#### PHP CodeSniffer

```bash
# Verificar
composer cs-check

# Corrigir automaticamente
composer cs-fix
```

### 7. Documentação

- Atualize o README.md se necessário
- Documente novas funcionalidades
- Adicione exemplos de uso
- Atualize o CHANGELOG.md

### 8. Pull Request

#### Checklist

- [ ] Código segue os padrões PSR-12
- [ ] Testes passam
- [ ] Cobertura de testes mantida
- [ ] Análise estática sem erros
- [ ] Documentação atualizada
- [ ] CHANGELOG atualizado
- [ ] Descrição clara do PR

#### Template do PR

```markdown
## Descrição
Breve descrição das mudanças

## Tipo de Mudança
- [ ] Bug fix
- [ ] Nova funcionalidade
- [ ] Breaking change
- [ ] Documentação

## Testes
- [ ] Testes unitários adicionados/atualizados
- [ ] Testes de integração passam
- [ ] Cobertura mantida

## Checklist
- [ ] Código segue padrões
- [ ] Self-review realizado
- [ ] Documentação atualizada
- [ ] CHANGELOG atualizado
```

## 🐛 Reportando Bugs

### Template de Bug Report

```markdown
**Descrição do Bug**
Descrição clara e concisa do bug

**Passos para Reproduzir**
1. Vá para '...'
2. Clique em '...'
3. Veja o erro

**Comportamento Esperado**
O que deveria acontecer

**Comportamento Atual**
O que realmente acontece

**Screenshots**
Se aplicável

**Ambiente**
- PHP: 8.2
- SDK Version: 1.0.0
- Sistema Operacional: Ubuntu 20.04

**Informações Adicionais**
Qualquer contexto adicional
```

## 💡 Sugerindo Funcionalidades

### Template de Feature Request

```markdown
**Problema**
Descrição do problema que a funcionalidade resolveria

**Solução Proposta**
Descrição da solução desejada

**Alternativas Consideradas**
Outras soluções consideradas

**Contexto Adicional**
Qualquer contexto adicional
```

## 📞 Suporte

- **Issues**: [GitHub Issues](https://github.com/Codijo/relayo-php-sdk/issues)
- **Email**: dev@codijo.com.br
- **Documentação**: [README.md](README.md)

## 📝 Licença

Ao contribuir, você concorda que suas contribuições serão licenciadas sob a licença MIT.

## 🙏 Agradecimentos

Obrigado por contribuir com o Relayo PHP SDK! Suas contribuições ajudam a tornar o SDK melhor para todos. 