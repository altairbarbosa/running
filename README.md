# Running

Monorepo do sistema de gestão de academia Running, separado em API Laravel e SPA React.

## Stack

- Backend: Laravel 13 / PHP 8.4 em `server/`
- Frontend: React + Vite + Tailwind CSS em `client/`
- Autenticação: Laravel Sanctum com cookies de SPA
- Banco: MySQL 8.4
- Infra: Docker Compose, Nginx e Mailpit

## Estrutura

```text
running/
├── client/              # SPA React
├── server/              # API Laravel
├── docker/              # Dockerfiles e Nginx
└── docker-compose.yml   # Orquestração local
```

## Executar

```bash
docker compose up -d --build
docker compose exec api composer install
docker compose exec api php artisan key:generate
docker compose exec api php artisan migrate --seed
```

Acesse:

- Frontend: <http://localhost:5174>
- Backend/API: <http://localhost:8080/api>
- Mailpit: <http://localhost:8026>

Credenciais de demonstração:

- Administrador: `admin@running.test` / `password`
- Aluno: `aluno@running.test` / `password`

## Comandos úteis

```bash
docker compose exec api php artisan test
docker compose exec api php artisan migrate:fresh --seed
docker compose exec api ./vendor/bin/pint
docker compose exec front npm run build
docker compose exec api php artisan billing:generate
```

O serviço `scheduler` executa a geração de cobranças automaticamente todos os dias.
