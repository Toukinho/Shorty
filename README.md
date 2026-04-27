# Shorty - URL Shortener API

A modern and robust API for creating and managing shortened URLs, built with Laravel 11 and Sanctum for secure token-based authentication.

## 📋 Features

- ✅ Create shortened URLs from long URLs
- ✅ Generate unique and random codes (6 characters)
- ✅ List all created URLs
- ✅ Retrieve information for a specific URL
- ✅ Track the number of accesses per URL
- ✅ API token authentication (Sanctum)
- ✅ Robust URL validation
- ✅ Prevention of duplicate URLs

## 🛠️ Requirements

- PHP 8.2 or higher
- Composer
- Laravel 11
- SQLite or MySQL
- Node.js (optional, for assets)

## 📦 Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd shorty
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure environment variables

```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file as needed:

```env
APP_NAME="Shorty"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=shorty
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Start the development server

```bash
php artisan serve
```

The server will be available at `http://localhost:8000`

## 📁 Project Structure

```
shorty/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── UrlController.php       # Main API controller
│   ├── Models/
│   │   └── Url.php                     # URL model with code generation
│   └── Providers/
│       └── AppServiceProvider.php      # Service configuration
├── config/
│   ├── app.php                         # Application configuration
│   └── sanctum.php                     # Authentication configuration
├── database/
│   ├── factories/
│   │   └── UrlFactory.php              # Factory for tests
│   ├── migrations/
│   │   ├── 2026_04_24_155727_create_personal_access_tokens_table.php
│   │   └── 2026_04_24_160002_create_urls_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   ├── api.php                         # API routes
│   └── web.php                         # Web routes
└── tests/                              # Automated tests
```

## 🔌 API Endpoints

### Base URL
```
http://localhost:8000/api/url
```

### 1. List all URLs

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

### 2. Create a shortened URL

**Request:**
```http
POST /api/url
Content-Type: application/json

{
  "url": "https://www.example.com/very/long/url/path"
}
```

**Parameters:**
- `url` (required, string, max: 2048): Long URL to be shortened. Must be a valid and unique URL.

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

**Possible errors:**
- `422 Unprocessable Entity`: Invalid URL or URL already exists
- `400 Bad Request`: 'url' field is missing

---

### 3. Retrieve a specific URL

**Request:**
```http
GET /api/url/{id}
```

**Parameters:**
- `id` (required, integer): URL ID

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

**Possible errors:**
- `404 Not Found`: URL with this ID does not exist

---

### 4. Update a URL

**Request:**
```http
PUT /api/url/{id}
Content-Type: application/json

{
  "url": "https://www.newurl.com/updated"
}
```

**Parameters:**
- `id` (required, integer): URL ID
- `url` (optional, string): New URL for the record

**Response:**
```json
{
  "message": "URL updated successfully",
  "data": { ... }
}
```

---

### 5. Delete a URL

**Request:**
```http
DELETE /api/url/{id}
```

**Parameters:**
- `id` (required, integer): URL ID to delete

**Response:**
```json
{
  "message": "URL deleted successfully"
}
```

## 🔐 Authentication

The API uses **Laravel Sanctum** for token-based authentication.

### Generate an access token

```bash
php artisan tinker
```

```php
$user = User::first();
$token = $user->createToken('api-token')->plainTextToken;
echo $token;
```

### Use the token in requests

Add the `Authorization` header with the Bearer token:

```bash
curl -X GET http://localhost:8000/api/url \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

## 📊 Data Model

### Table: `urls`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Unique URL ID |
| `url` | string | Original URL (maximum 2048 characters) |
| `short_url` | string | Unique 6-character code |
| `access_count` | unsignedInteger | Number of accesses (default: 0) |
| `created_at` | timestamp | Creation date |
| `updated_at` | timestamp | Last update date |

### Automatic short code generation

The short code is generated automatically when a URL is created. The system:

1. Generates a random 6-character code
2. Checks if it already exists in the database
3. If it exists, regenerates until finding a unique code

```php
// Generated automatically in the Url Model
protected static function booted(): void
{
    static::creating(function (Url $url) {
        $url->short_url = self::generateUniqueShortUrl();
    });
}
```

## 🧪 Tests

### Run tests

```bash
php artisan test
```

### Run with coverage

```bash
php artisan test --coverage
```

### Use Factory for tests

```bash
php artisan tinker
```

```php
// Create 10 test URLs
Url::factory()->count(10)->create();

// Create with specific data
Url::factory()->create([
  'url' => 'https://example.com',
]);
```

## 🚀 Complete Usage Example

### 1. Create a shortened URL

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

### 2. List all URLs

```bash
curl -X GET http://localhost:8000/api/url
```

### 3. Get a specific URL

```bash
curl -X GET http://localhost:8000/api/url/1
```

### 4. Update a URL

```bash
curl -X PUT http://localhost:8000/api/url/1 \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://laravel.com"
  }'
```

### 5. Delete a URL

```bash
curl -X DELETE http://localhost:8000/api/url/1
```

## 📝 Validation Rules

URLs are validated with the following rules:

- `required`: Field is mandatory
- `url`: Must be a valid URL
- `max:2048`: Maximum length of 2048 characters
- `unique:urls,url`: The URL cannot be duplicated in the database

## 🔧 Advanced Configuration

### Change short code length

To change the generated code length (default: 6 characters), edit [app/Models/Url.php](app/Models/Url.php):

```php
// Change from 6 to another value
$code = Str::random(8); // For 8 characters
```

### Customize short URL prefix

To add a custom prefix to shortened URLs, modify the `store` method in [app/Http/Controllers/UrlController.php](app/Http/Controllers/UrlController.php):

```php
'short_url' => url('s/' . $url->short_url), // Adds /s/ prefix
```

## 🐛 Troubleshooting

### Error: "The application does not have a default cache, database, or queue connection set."

Solution: Configure the `.env` file:
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Error: "SQLSTATE[HY000]: General error: 1 no such table: urls"

Solution: Run the migrations:
```bash
php artisan migrate
```

### Error: "The POST method is not supported for this route"

Check if you are using the correct HTTP method and URL: `POST /api/url`

## 📚 References

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [https://roadmap.sh/projects/url-shortening-service]

## 📄 License

This project is licensed under the MIT License. See the LICENSE file for more details.

## 👤 Author

Developed as a demonstration API for managing shortened URLs.

---

**Last updated:** April 27, 2026
