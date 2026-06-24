#!/bin/bash
# =============================================================================
#  PRISMA — Docker Entrypoint
#  Inicializa o container com as configurações corretas do Laravel
# =============================================================================

set -e

echo "──────────────────────────────────────────────"
echo "  🚀 PRISMA — Inicializando container"
echo "──────────────────────────────────────────────"

# Aguarda o banco estar disponível
wait_for_db() {
    echo "⏳ Aguardando MySQL..."
    until php -r "new PDO('mysql:host=mysql;port=3306;dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
        sleep 2
    done
    echo "✅ MySQL disponível!"
}

# Aguarda Redis
wait_for_redis() {
    echo "⏳ Aguardando Redis..."
    until php -r "
        \$r = new Redis();
        \$r->connect('redis', 6379);
        \$r->auth('${REDIS_PASSWORD:-redispass}');
        echo 'ok';
    " 2>/dev/null | grep -q 'ok'; do
        sleep 2
    done
    echo "✅ Redis disponível!"
}

# Só roda migrações / otimizações no container principal (não no queue/scheduler)
if [[ "${CONTAINER_ROLE:-app}" == "app" ]]; then
    wait_for_db
    wait_for_redis

    echo "🔑 Gerando APP_KEY se necessário..."
    php artisan key:generate --no-interaction --force 2>/dev/null || true

    echo "📦 Publicando assets..."
    php artisan vendor:publish --tag=laravel-assets --no-interaction --force 2>/dev/null || true

    echo "🗄️  Rodando migrações..."
    php artisan migrate --force --no-interaction

    echo "🗂️  Criando link simbólico de storage..."
    php artisan storage:link --force 2>/dev/null || true

    if [[ "${APP_ENV}" == "production" ]]; then
        echo "⚡ Otimizando para produção..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan event:cache
    else
        echo "🧹 Limpando caches (ambiente dev)..."
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
    fi
fi

echo "──────────────────────────────────────────────"
echo "  ✅ PRISMA pronto! Iniciando: $@"
echo "──────────────────────────────────────────────"

exec "$@"
