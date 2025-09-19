# Backend (Laravel 12)

API REST construída em Laravel 12 usando Laravel Sanctum para autenticação via Bearer Token. Banco de dados padrão: PostgreSQL.

## Requisitos

- PHP 8.2+
- Composer 2+
- Extensões PHP comuns (pdo, pdo_sqlite etc.)
- Opcional: Node.js se quiser rodar o Vite local do Laravel (não é necessário para a API)

## Configuração (Windows e Linux)

1. Copie o .env
   - Linux/Git Bash (Windows): `cp .env.example .env`
   - PowerShell (Windows): `Copy-Item .env.example .env`

2. Configure o PostgreSQL
   - Abra `.env` e confirme/ajuste:
     - `DB_CONNECTION=pgsql`
     - `DB_HOST=127.0.0.1`
     - `DB_PORT=5432`
     - `DB_DATABASE=laravel`
     - `DB_USERNAME=root`
     - `DB_PASSWORD=password`

3. Instale dependências e gere chave
   - `composer install`
   - `php artisan key:generate`

4. Crie o banco e rode migrações/seed
   - Crie banco/usuário no PostgreSQL (com superusuário, ex.: `postgres`):
     - Linux:
       - `sudo -u postgres psql -c "CREATE DATABASE laravel;"`
       - `sudo -u postgres psql -c "CREATE USER root WITH PASSWORD 'password';"`
       - `sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE laravel TO root;"`
     - Windows (psql no PATH):
       - `psql -U postgres -h 127.0.0.1 -p 5432 -c "CREATE DATABASE laravel;"`
       - `psql -U postgres -h 127.0.0.1 -p 5432 -c "CREATE USER root WITH PASSWORD 'password';"`
       - `psql -U postgres -h 127.0.0.1 -p 5432 -c "GRANT ALL PRIVILEGES ON DATABASE laravel TO root;"`
   - Rode as migrações e seed:
     - `php artisan migrate --seed`

5. Rode o servidor
   - `php artisan serve`
   - URL: [http://127.0.0.1:8000](http://127.0.0.1:8000) (API: [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api))

Usuários seedados (senha: `Password@123`):

- `carlos@example.com`
- `fernanda@example.com`
- `isabela@example.com`
- `rafael@example.com`
- `beatriz@example.com`
- `rodrigo@example.com`

## Variáveis de ambiente úteis

- `APP_URL=http://localhost`
- `ALLOWED_ORIGINS=http://localhost:3000,http://localhost`
- `DB_*` (ver exemplos acima para PostgreSQL)
- `SESSION_DRIVER=database` (já configurado por padrão)

CORS: Por padrão, o Laravel inclui um middleware de CORS. Existe também um middleware opcional `App\Http\Middleware\SecureApiHeaders` que lê `ALLOWED_ORIGINS` do `.env` e responde a preflight (OPTIONS). Se decidir adicioná-lo ao Kernel, inclua a origem do frontend em `ALLOWED_ORIGINS`.

## Endpoints

Base URL: `/api`

Auth

- POST `/api/register`
  - Body JSON: `{ "name": string, "email": string, "password": string, "password_confirmation": string }`
  - Resposta: `{ data: { token_type, token, user } }`
- POST `/api/login`
  - Body JSON: `{ "email": string, "password": string }`
  - Resposta: `{ data: { token_type, token, user } }`
- POST `/api/logout` (requer Authorization: Bearer `<token>`)
  - Resposta: `{ message }`
- GET `/api/user` (requer Authorization)
  - Resposta: `{ data: { user: { id, name, email } } }`

Tweets (requer Authorization)

- GET `/api/tweets?page=1`
  - Resposta: paginação Laravel com tweets e relação user
- POST `/api/tweets`
  - Body JSON: `{ "content": string (<= 280), "visibility": "public" | "private" }`
  - Resposta: tweet criado com user incluso
- PUT `/api/tweets/{id}`
  - Body JSON: `{ "content": string, "visibility": "public" | "private" }`
  - Resposta: tweet atualizado
- DELETE `/api/tweets/{id}`
  - Resposta: `{ message }`

## Exemplos rápidos de cURL

Login:

- `curl -X POST http://127.0.0.1:8000/api/login -H "Content-Type: application/json" -d '{"email":"carlos@example.com","password":"Password@123"}'`

Listar tweets (com token):

- `curl http://127.0.0.1:8000/api/tweets -H "Authorization: Bearer <TOKEN>"`

## Testes

Como rodar os testes passo a passo (Windows/Linux):

1. Pré-requisitos

- PHP 8.2+ com extensões sqlite3 e pdo_sqlite habilitadas
- Composer 2+
- Dependências instaladas no diretório `backend/`

No Windows, verifique no seu `php.ini` se as linhas estão ativas (sem ponto e vírgula):

```ini
extension=sqlite3
extension=pdo_sqlite
```

1. Instalar dependências do projeto (apenas na primeira vez)

- Dentro de `backend/` execute:

```bash
composer install
```

1. Gerar a APP_KEY (se ainda não gerou)

```bash
php artisan key:generate
```

1. Rodar toda a suíte de testes

- Com limpeza de cache + runner padrão do Laravel:

```bash
composer test
```

- Alternativa (sem o script do Composer):

```bash
php artisan config:clear
php artisan test
```

Observação: os testes usam SQLite em memória por padrão (ver `phpunit.xml` com `DB_CONNECTION=sqlite` e `DB_DATABASE=:memory:`), então eles NÃO afetam seu banco de desenvolvimento.

1. Executar um arquivo de teste específico

```bash
php artisan test tests/Feature/AlgumTeste.php
```

Ou usando o Pest diretamente:

```bash
vendor/bin/pest tests/Unit/TweetTest.php
```

1. Filtrar por nome do teste

- Via Artisan (PHPUnit):

```bash
php artisan test --filter=NomeDoMetodoOuClasse
```

- Via Pest (por expressão do título):

```bash
vendor/bin/pest -t "parte do título do teste"
```

1. Ver cobertura de código (opcional)

Para cobertura, habilite o Xdebug ou PCOV. Com Xdebug ativo:

```bash
php -d xdebug.mode=coverage vendor/bin/pest --coverage
```

Ou com o runner do Laravel:

```bash
php -d xdebug.mode=coverage artisan test --coverage
```

1. Dicas e problemas comuns

- Erro de driver/SQLite: certifique-se de que `sqlite3` e `pdo_sqlite` estão habilitados no `php.ini`.
- Cache de config: o script `composer test` já limpa o cache (`config:clear`). Se executar apenas `php artisan test` e notar comportamento estranho, rode `php artisan config:clear` antes.
- Banco real sendo usado: se você quiser forçar o uso do banco de testes em memória, confira seu `phpunit.xml` (já configurado neste projeto). Evite sobrescrever `DB_CONNECTION` via `.env` quando `APP_ENV=testing`.

## Troubleshooting

- 401 Unauthorized nas rotas de tweets: verifique se o header `Authorization: Bearer <token>` está sendo enviado.
- CORS via navegador: ajuste `ALLOWED_ORIGINS` no `.env` se estiver usando o middleware `SecureApiHeaders` e confirme a origem (ex.: `http://localhost:3000`). Se não estiver usando esse middleware customizado, o CORS padrão do Laravel normalmente cobre as necessidades do dev.
- Banco PostgreSQL: confirme se o banco/usuário foram criados e se `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` estão corretos.

## (Opcional) Usar SQLite no desenvolvimento

Se preferir SQLite no ambiente local:

1. Ajuste `.env`:

- `DB_CONNECTION=sqlite`
- `DB_DATABASE=database/database.sqlite`

1. Criar arquivo de banco e migrar:

- `php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"`
- `php artisan migrate --seed`
