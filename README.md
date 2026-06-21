# Running

Reescrita moderna do sistema acadêmico de gestão de academias, com o projeto original preservado em `./vanilla`.

## Stack

- Laravel 13 / PHP 8.4
- Blade, Alpine.js, Tailwind CSS e Vite
- MySQL 8.4
- Nginx, Mailpit e Docker Compose

## Executar

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

A aplicação estará em <http://localhost:8080> e o Mailpit em <http://localhost:8026>.

Credenciais de demonstração:

- Administrador: `admin@running.test` / `password`
- Aluno: `aluno@running.test` / `password`

## Comandos úteis

```bash
docker compose exec app php artisan test
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app ./vendor/bin/pint
docker compose exec node npm run build
```

## Escopo atual

- autenticação e autorização por perfil;
- cadastro de alunos;
- catálogo de exercícios;
- elaboração e consulta de fichas de treino;
- planos e matrículas com preço histórico;
- geração automática e idempotente de cobranças;
- pagamentos parciais e totais;
- controle de vencimentos e inadimplência;
- perfil pessoal com foto e alteração segura de senha;
- gestão administrativa de usuários, papéis e acessos;
- interface responsiva para administração e aluno.

Para gerar cobranças manualmente:

```bash
docker compose exec app php artisan billing:generate
```

O serviço `scheduler` executa essa atualização automaticamente todos os dias. A migração dos dados financeiros legados será implementada em uma etapa própria.
