#!/bin/bash

# Script para criar release do SDK Relayo
# Uso: ./scripts/release.sh [major|minor|patch]

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para imprimir mensagens coloridas
print_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Verificar se o tipo de release foi especificado
if [ -z "$1" ]; then
    print_error "Tipo de release não especificado!"
    echo "Uso: $0 [major|minor|patch]"
    echo ""
    echo "Exemplos:"
    echo "  $0 patch  # 1.0.0 -> 1.0.1"
    echo "  $0 minor  # 1.0.0 -> 1.1.0"
    echo "  $0 major  # 1.0.0 -> 2.0.0"
    exit 1
fi

RELEASE_TYPE=$1

# Verificar se estamos na branch main
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ]; then
    print_error "Você deve estar na branch 'main' para criar um release!"
    echo "Branch atual: $CURRENT_BRANCH"
    exit 1
fi

# Verificar se há mudanças não commitadas
if [ -n "$(git status --porcelain)" ]; then
    print_error "Há mudanças não commitadas no repositório!"
    echo "Por favor, commit ou stash suas mudanças antes de continuar."
    exit 1
fi

print_step "Iniciando processo de release..."

# Atualizar branch main
print_message "Atualizando branch main..."
git pull origin main

# Executar testes
print_step "Executando testes..."
composer test

# Executar análise estática
print_step "Executando análise estática..."
composer phpstan

# Executar verificação de estilo
print_step "Verificando estilo de código..."
composer cs-check

# Obter versão atual
CURRENT_VERSION=$(composer show -s | grep "version" | awk '{print $2}')
print_message "Versão atual: $CURRENT_VERSION"

# Calcular nova versão
if [ "$RELEASE_TYPE" = "major" ]; then
    NEW_VERSION=$(echo $CURRENT_VERSION | awk -F. '{print $1+1 ".0.0"}')
elif [ "$RELEASE_TYPE" = "minor" ]; then
    NEW_VERSION=$(echo $CURRENT_VERSION | awk -F. '{print $1 "." $2+1 ".0"}')
elif [ "$RELEASE_TYPE" = "patch" ]; then
    NEW_VERSION=$(echo $CURRENT_VERSION | awk -F. '{print $1 "." $2 "." $3+1}')
else
    print_error "Tipo de release inválido: $RELEASE_TYPE"
    exit 1
fi

print_message "Nova versão: $NEW_VERSION"

# Confirmar com o usuário
echo ""
print_warning "Você está prestes a criar o release v$NEW_VERSION"
read -p "Continuar? (y/N): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_message "Release cancelado."
    exit 0
fi

# Atualizar CHANGELOG.md
print_step "Atualizando CHANGELOG.md..."
TODAY=$(date +%Y-%m-%d)
sed -i.bak "s/## \[Unreleased\]/## \[Unreleased\]\n\n## \[$NEW_VERSION\] - $TODAY/" CHANGELOG.md
rm CHANGELOG.md.bak

# Commit das mudanças
print_step "Commitando mudanças..."
git add CHANGELOG.md
git commit -m "chore: prepare release v$NEW_VERSION"

# Criar tag
print_step "Criando tag v$NEW_VERSION..."
git tag -a "v$NEW_VERSION" -m "Release v$NEW_VERSION"

# Push das mudanças e tag
print_step "Enviando mudanças para o GitHub..."
git push origin main
git push origin "v$NEW_VERSION"

print_message "✅ Release v$NEW_VERSION criado com sucesso!"
echo ""
print_message "O GitHub Actions irá:"
echo "  1. Executar todos os testes"
echo "  2. Criar o release no GitHub"
echo "  3. Notificar o Packagist para atualizar o pacote"
echo ""
print_message "Você pode acompanhar o progresso em:"
echo "  https://github.com/Codijo/relayo-php-sdk/actions"
echo ""
print_message "Após a conclusão, o pacote estará disponível em:"
echo "  https://packagist.org/packages/codijo/relayo-php-sdk"
echo ""
print_message "Para instalar:"
echo "  composer require codijo/relayo-php-sdk" 