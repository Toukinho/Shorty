# Shorty - URL Shortener API

Uma API moderna e robusta para criar e gerenciar URLs curtas, construída com Laravel 11 e Sanctum para autenticação segura baseada em tokens.

## 📋 Funcionalidades

- ✅ Criar URLs curtas a partir de URLs longas
- ✅ Gerar códigos únicos e aleatórios (6 caracteres)
- ✅ Listar todas as URLs criadas
- ✅ Recuperar informações de uma URL específica
- ✅ Rastrear número de acessos por URL
- ✅ Autenticação via API tokens (Sanctum)
- ✅ Validação robusta de URLs
- ✅ Prevenção de URLs duplicadas

## 🛠️ Requisitos

- PHP 8.2 ou superior
- Composer
- Laravel 11
- SQLite ou MySQL
- Node.js (opcional, para assets)

## 📦 Instalação

### 1. Clonar o repositório

```bash
git clone <repository-url>
cd shorty
```

### 2. Instalar dependências

```bash
composer install
npm install
```

### 3. Configurar variáveis de ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Edite o arquivo `.env` conforme necessário:

```env
APP_NAME="Shorty"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# ou para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=shorty
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Executar migrações

```bash
php artisan migrate
```

### 5. Iniciar o servidor de desenvolvimento

```bash
php artisan serve
```

O servidor estará disponível em `http://localhost:8000`

## 📁 Estrutura do Projeto

```
shorty/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── UrlController.php       # Controller principal da API
│   ├── Models/
│   │   └── Url.php                     # Model da URL com geração de código
│   └── Providers/
│       └── AppServiceProvider.php      # Configuração de serviços
├── config/
│   ├── app.php                         # Configuração da aplicação
│   └── sanctum.php                     # Configuração de autenticação
├── database/
│   ├── factories/
│   │   └── UrlFactory.php              # Factory para testes
│   ├── migrations/
│   │   ├── 2026_04_24_155727_create_personal_access_tokens_table.php
│   │   └── 2026_04_24_160002_create_urls_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   ├── api.php                         # Rotas da API
│   └── web.php                         # Rotas web
└── tests/                              # Testes automatizados
```

## 🔌 API Endpoints

### Base URL
```
http://localhost:8000/api/url
```

### 1. Listar todas as URLs

**Request:**
```http
GET /api/url
```

**Response:**
```json
{
  "message": "URLs retrieved successfully",
  "data": [
    {
      "id": 1,
      "url": "https://www.example.com/very/long/url",
      "short_url": "abc123",
      "access_count": 0,
      "created_at": "2026-04-27T10:30:00.000000Z",
      "updated_at": "2026-04-27T10:30:00.000000Z"
    }
  ]
}
```

---

### 2. Criar uma URL curta

**Request:**
```http
POST /api/url
Content-Type: application/json

{
  "url": "https://www.example.com/very/long/url/path"
}
```

**Parameters:**
- `url` (required, string, max: 2048): URL longa a ser encurtada. Deve ser uma URL válida e única.

**Response (201 Created):**
```json
{
  "message": "URL created successfully",
  "data": {
    "original_url": "https://www.example.com/very/long/url/path",
    "short_url": "http://localhost:8000/abc123",
    "access_count": 0
  }
}
```

**Possíveis erros:**
- `422 Unprocessable Entity`: URL inválida ou já existe
- `400 Bad Request`: Campo 'url' ausente

---

### 3. Recuperar uma URL específica

**Request:**
```http
GET /api/url/{id}
```

**Parameters:**
- `id` (required, integer): ID da URL

**Response:**
```json
{
  "message": "URL retrieved successfully",
  "data": {
    "original_url": "https://www.example.com/very/long/url/path",
    "short_url": "http://localhost:8000/abc123",
    "access_count": 5
  }
}
```

**Possíveis erros:**
- `404 Not Found`: URL com este ID não existe

---

### 4. Atualizar uma URL

**Request:**
```http
PUT /api/url/{id}
Content-Type: application/json

{
  "url": "https://www.newurl.com/updated"
}
```

**Parameters:**
- `id` (required, integer): ID da URL
- `url` (optional, string): Nova URL para o registro

**Response:**
```json
{
  "message": "URL updated successfully",
  "data": { ... }
}
```

---

### 5. Deletar uma URL

**Request:**
```http
DELETE /api/url/{id}
```

**Parameters:**
- `id` (required, integer): ID da URL a deletar

**Response:**
```json
{
  "message": "URL deleted successfully"
}
```

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** para autenticação baseada em tokens.

### Gerar um token de acesso

```bash
php artisan tinker
```

```php
$user = User::first();
$token = $user->createToken('api-token')->plainTextToken;
echo $token;
```

### Usar o token em requisições

Adicione o header `Authorization` com o token Bearer:

```bash
curl -X GET http://localhost:8000/api/url \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

## 📊 Modelo de Dados

### Tabela: `urls`

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | bigint | ID único da URL |
| `url` | string | URL original (máximo 2048 caracteres) |
| `short_url` | string | Código único de 6 caracteres |
| `access_count` | unsignedInteger | Número de acessos (padrão: 0) |
| `created_at` | timestamp | Data de criação |
| `updated_at` | timestamp | Data da última atualização |

### Geração automática de código curto

O código curto é gerado automaticamente quando uma URL é criada. O sistema:

1. Gera um código aleatório de 6 caracteres
2. Verifica se já existe no banco de dados
3. Se existir, regenera até encontrar um código único

```php
// Gerado automaticamente no Model Url
protected static function booted(): void
{
    static::creating(function (Url $url) {
        $url->short_url = self::generateUniqueShortUrl();
    });
}
```

## 🧪 Testes

### Executar testes

```bash
php artisan test
```

### Executar com cobertura

```bash
php artisan test --coverage
```

### Usar Factory para testes

```bash
php artisan tinker
```

```php
// Criar 10 URLs de teste
Url::factory()->count(10)->create();

// Criar com dados específicos
Url::factory()->create([
  'url' => 'https://example.com',
]);
```

## 🚀 Exemplo de Uso Completo

### 1. Criar uma URL curta

```bash
curl -X POST http://localhost:8000/api/url \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://github.com/laravel/laravel/blob/master/README.md"
  }'
```

**Response:**
```json
{
  "message": "URL created successfully",
  "data": {
    "original_url": "https://github.com/laravel/laravel/blob/master/README.md",
    "short_url": "http://localhost:8000/xY9kZm",
    "access_count": 0
  }
}
```

### 2. Listar todas as URLs

```bash
curl -X GET http://localhost:8000/api/url
```

### 3. Obter uma URL específica

```bash
curl -X GET http://localhost:8000/api/url/1
```

### 4. Atualizar uma URL

```bash
curl -X PUT http://localhost:8000/api/url/1 \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://laravel.com"
  }'
```

### 5. Deletar uma URL

```bash
curl -X DELETE http://localhost:8000/api/url/1
```

## 📝 Variáveis de Validação

As URLs são validadas com as seguintes regras:

- `required`: Campo obrigatório
- `url`: Deve ser uma URL válida
- `max:2048`: Comprimento máximo de 2048 caracteres
- `unique:urls,url`: A URL não pode ser duplicada no banco de dados

## 🔧 Configuração avançada

### Modificar comprimento do código curto

Para alterar o comprimento do código gerado (padrão: 6 caracteres), edite [app/Models/Url.php](app/Models/Url.php):

```php
// Alterar de 6 para outro valor
$code = Str::random(8); // Para 8 caracteres
```

### Customizar prefixo da URL curta

Para adicionar um prefixo personalizado às URLs curtas, modifique o método `store` em [app/Http/Controllers/UrlController.php](app/Http/Controllers/UrlController.php):

```php
'short_url' => url('s/' . $url->short_url), // Adiciona prefixo /s/
```

## 🐛 Troubleshooting

### Erro: "The application does not have a default cache, database, or queue connection set."

Solução: Configure o arquivo `.env`:
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Erro: "SQLSTATE[HY000]: General error: 1 no such table: urls"

Solução: Execute as migrações:
```bash
php artisan migrate
```

### Erro: "The POST method is not supported for this route"

Verificar se está usando o método HTTP correto e a URL correta: `POST /api/url`

## 📚 Referências

- [Documentação Laravel](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Eloquent ORM](https://laravel.com/docs/eloquent)

## 📄 Licença

Este projeto é licenciado sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 👤 Autor

Desenvolvido como uma API de demonstração para gerenciamento de URLs curtas.

---

**Última atualização:** 27 de abril de 2026
