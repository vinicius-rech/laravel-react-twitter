# Laravel + Next.js Twitter-like App

Este repositório contém um backend em Laravel 12 (API com Sanctum) e um frontend em Next.js 15. Ele demonstra autenticação (login, registro, logout) e CRUD de Tweets com visibilidade pública/privada.

- Backend: `backend/` (Laravel 12, PHP 8.2+, PostgreSQL por padrão)
- Frontend: `frontend/` (Next.js 15, React 19, TypeScript)

Para detalhes de cada parte, consulte também:

- `backend/README.md` — documentação da API (instalação, .env, migrações, endpoints)
- `frontend/README.md` — documentação do app web (instalação, variáveis, build)

## Requisitos

Windows e Linux:

- Git
- Node.js 18+ (recomendado 20 LTS)
- npm 9+ (ou pnpm/yarn)
- PHP 8.2+
- Composer 2+

Opcional: Postman/Insomnia, cURL.

## Guia rápido (desenvolvimento)

Suba a API (Laravel) e o frontend (Next.js). O backend está configurado para PostgreSQL por padrão.

1. Backend

   - Copie o .env:
     - Bash (Linux ou Git Bash no Windows): `cp backend/.env.example backend/.env`
     - PowerShell (Windows): `Copy-Item backend/.env.example backend/.env`
   - Configure PostgreSQL no `backend/.env` (valores padrão do exemplo):
     - `DB_CONNECTION=pgsql`
     - `DB_HOST=127.0.0.1`
     - `DB_PORT=5432`
     - `DB_DATABASE=laravel`
     - `DB_USERNAME=root`
     - `DB_PASSWORD=password`
   - Crie o banco e usuário no PostgreSQL (execute com um superusuário, ex.: `postgres`):
     - Linux (Ubuntu/Debian):

       ```bash
       sudo -u postgres psql -c "CREATE DATABASE laravel;"
       sudo -u postgres psql -c "CREATE USER root WITH PASSWORD 'password';"
       sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE laravel TO root;"
       ```

     - Windows (PowerShell, com psql no PATH):

       ```bash
       psql -U postgres -h 127.0.0.1 -p 5432 -c "CREATE DATABASE laravel;"
       psql -U postgres -h 127.0.0.1 -p 5432 -c "CREATE USER root WITH PASSWORD 'password';"
       psql -U postgres -h 127.0.0.1 -p 5432 -c "GRANT ALL PRIVILEGES ON DATABASE laravel TO root;"
       ```

   - Dentro de `backend/` execute:
     - `composer install`
     - `php artisan key:generate`
     - `php artisan migrate --seed`
   - Inicie o servidor: `php artisan serve` (acesso em [http://127.0.0.1:8000](http://127.0.0.1:8000))

2. Frontend

   - Crie `frontend/.env.local` com:
     - `NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api`
   - Dentro de `frontend/` execute:
     - `npm install`
     - `npm run dev` (acesso em [http://localhost:3000](http://localhost:3000))

3. Acesse

   - App: [http://localhost:3000](http://localhost:3000)
   - API base: [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)

Usuários seedados (senha: `Password@123`):

- `carlos@example.com`
- `fernanda@example.com`
- `isabela@example.com`
- `rafael@example.com`
- `beatriz@example.com`
- `rodrigo@example.com`

## Dicas importantes (Windows/Linux)

- CORS: o Laravel já inclui tratamento padrão. Se ativar o middleware opcional `SecureApiHeaders`, ajuste `ALLOWED_ORIGINS` no `.env` para incluir a origem do frontend (ex.: `http://localhost:3000`).
- Portas padrão: API 8000 e Frontend 3000. Conflitos podem ser resolvidos alterando a porta (ex.: `php artisan serve --port=8001`) e atualizando `NEXT_PUBLIC_API_URL` no frontend.
- Banco: SQLite é o caminho mais fácil no dev. Para MySQL/Postgres, ajuste `DB_*` no `.env` e rode as migrações novamente.

## Documentação detalhada

- `backend/README.md`: variáveis de ambiente, migrações, seeds, endpoints, exemplos de cURL e troubleshooting.
- `frontend/README.md`: variáveis `NEXT_PUBLIC_*`, scripts, build e troubleshooting.

## Testes (atalho)

- Back-end (Laravel): dentro de `backend/` execute:

```bash
composer test
```

Guia completo de testes: veja "Testes" em `backend/README.md`.

## Licença

MIT
