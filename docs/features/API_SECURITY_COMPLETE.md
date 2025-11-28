# API Security - Complete Implementation

## Overview
Comprehensive API security implementation with Sanctum authentication, role-based rate limiting, and security headers.

## 1. Laravel Sanctum Setup

### Installation
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Configuration
- **Token expiration:** No expiration (configurable in `config/sanctum.php`)
- **Stateful domains:** localhost, 127.0.0.1, production domains
- **Guards:** web, api

### User Model
Added `HasApiTokens` trait to User model for token management.

## 2. Authentication Endpoints

### Base URL
All API endpoints are prefixed with `/api/v1`

### Public Routes (No authentication required)

#### POST /api/v1/login
Authenticate user and generate API token.

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password",
    "device_name": "mobile-app" // optional
}
```

**Response (200):**
```json
{
    "success": true,
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "user"
    }
}
```

**Response (422 - Invalid credentials):**
```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": ["The provided credentials are incorrect."]
    }
}
```

**Rate Limit:** 10 requests/minute (strict)

### Protected Routes (Require authentication)

All protected routes require `Authorization: Bearer {token}` header.

#### POST /api/v1/logout
Revoke current access token.

**Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

#### GET /api/v1/user
Get authenticated user details.

**Response (200):**
```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        // ... full user object
    }
}
```

#### POST /api/v1/refresh
Refresh authentication token (revokes current, issues new).

**Request:**
```json
{
    "device_name": "mobile-app" // optional
}
```

**Response (200):**
```json
{
    "success": true,
    "token": "2|xyz789..."
}
```

**Rate Limit:** 200 requests/minute (relaxed)

## 3. Rate Limiting

### How It Works
Role-based rate limiting using Laravel's RateLimiter with different limits based on user roles and request types.

### Rate Limit Tiers

| User Type | Requests/Minute | Use Case |
|-----------|----------------|----------|
| Super Admin | 1000 | Administrative operations |
| Admin | 1000 | Management tasks |
| Agent/Service Provider | 500 | Business operations |
| Authenticated User | 100 | Standard API usage |
| Unauthenticated | 60 | Public endpoints |

### Custom Limits

#### Strict Limit
- **Limit:** 10 requests/minute
- **Applied to:** Login, registration, password reset
- **Purpose:** Prevent brute force attacks

#### Relaxed Limit
- **Limit:** 200 requests/minute
- **Applied to:** General API endpoints
- **Purpose:** Standard usage without role-based restrictions

### Usage
```php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('api.rate_limit:strict');

Route::get('/data', [DataController::class, 'index'])
    ->middleware(['auth:sanctum', 'api.rate_limit:relaxed']);
```

### Response Headers
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
```

### Rate Limit Exceeded (429)
```json
{
    "success": false,
    "message": "Too many requests. Please try again later.",
    "retry_after": 45
}
```
**Response Headers:**
```
Retry-After: 45
```

## 4. Security Headers

### Implemented Headers

#### Content-Security-Policy (CSP)
Prevents XSS attacks by restricting resource loading sources.
```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net;
style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
img-src 'self' data: https:;
font-src 'self' data: https://fonts.gstatic.com;
connect-src 'self';
frame-ancestors 'none';
```

#### Strict-Transport-Security (HSTS)
Forces HTTPS connections for 1 year.
```
max-age=31536000; includeSubDomains
```

#### X-Frame-Options
Prevents clickjacking attacks.
```
DENY
```

#### X-Content-Type-Options
Prevents MIME type sniffing.
```
nosniff
```

#### X-XSS-Protection
Enables browser XSS protection.
```
1; mode=block
```

#### Referrer-Policy
Controls referrer information sent with requests.
```
strict-origin-when-cross-origin
```

#### Permissions-Policy
Restricts browser features.
```
geolocation=(), microphone=(), camera=()
```

### Application
Security headers are automatically applied to all web routes via `SecurityHeadersMiddleware`.

## 5. Authentication Flow

### Token-Based Authentication

```
┌─────────┐                  ┌─────────┐
│ Client  │                  │  Server │
└────┬────┘                  └────┬────┘
     │                            │
     │  POST /api/v1/login        │
     │  {email, password}         │
     ├───────────────────────────>│
     │                            │
     │  200 OK                    │
     │  {token, user}             │
     │<───────────────────────────┤
     │                            │
     │  GET /api/v1/data          │
     │  Authorization: Bearer ... │
     ├───────────────────────────>│
     │                            │
     │  200 OK                    │
     │  {data}                    │
     │<───────────────────────────┤
     │                            │
     │  POST /api/v1/logout       │
     │  Authorization: Bearer ... │
     ├───────────────────────────>│
     │                            │
     │  200 OK                    │
     │  {success: true}           │
     │<───────────────────────────┤
     │                            │
```

### Token Management

#### Creating Tokens
```php
$token = $user->createToken('device-name')->plainTextToken;
```

#### Revoking Tokens
```php
// Revoke current token
$request->user()->currentAccessToken()->delete();

// Revoke all tokens
$user->tokens()->delete();

// Revoke specific token
$user->tokens()->where('id', $tokenId)->delete();
```

#### Checking Abilities
```php
if ($request->user()->tokenCan('server:update')) {
    // Has ability
}
```

## 6. Testing

### Using cURL

#### Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

#### Get User
```bash
curl -X GET http://localhost:8000/api/v1/user \
  -H "Authorization: Bearer 1|abc123..."
```

#### Logout
```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer 1|abc123..."
```

### Using Postman

1. **Login:**
   - Method: POST
   - URL: `http://localhost:8000/api/v1/login`
   - Body (JSON): `{"email":"admin@example.com","password":"password"}`
   - Save token from response

2. **Protected Endpoints:**
   - Add header: `Authorization: Bearer {token}`
   - Make requests to protected endpoints

### Testing Rate Limits

```bash
# Test strict limit (should fail after 10 requests)
for i in {1..15}; do
  curl -X POST http://localhost:8000/api/v1/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@example.com","password":"wrong"}'
  echo "Request $i"
done
```

## 7. Files Created/Modified

### Created Files
1. **app/Http/Controllers/API/AuthController.php**
   - Login endpoint
   - Logout endpoint
   - User details endpoint
   - Token refresh endpoint

2. **app/Http/Middleware/ApiRateLimitMiddleware.php**
   - Role-based rate limiting
   - Custom limit configurations
   - Rate limit headers

3. **app/Http/Middleware/SecurityHeadersMiddleware.php**
   - CSP, HSTS, X-Frame-Options
   - MIME sniffing prevention
   - XSS protection headers

### Modified Files
1. **app/Models/User.php**
   - Added `HasApiTokens` trait

2. **bootstrap/app.php**
   - Registered API middleware
   - Added Sanctum middleware
   - Registered middleware aliases

3. **routes/api.php**
   - Added v1 API routes
   - Applied rate limiting
   - Organized route groups

## 8. Security Best Practices

### Implemented ✅
- [x] Token-based authentication (Sanctum)
- [x] Role-based rate limiting
- [x] Security headers (CSP, HSTS, etc.)
- [x] HTTPS enforcement (HSTS)
- [x] Clickjacking prevention (X-Frame-Options)
- [x] XSS protection headers
- [x] MIME sniffing prevention
- [x] Token revocation on logout

### Recommended for Production
- [ ] Enable HTTPS/SSL certificates
- [ ] Set up Redis for rate limiting (better performance)
- [ ] Implement API key rotation
- [ ] Add request logging and monitoring
- [ ] Set up CORS properly for production domains
- [ ] Implement IP whitelisting for admin endpoints
- [ ] Add webhook signature verification
- [ ] Enable Laravel Passport for OAuth2 (if needed)

## 9. Environment Configuration

Add to `.env`:
```env
# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,yourdomain.com

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Security
APP_ENV=production
APP_DEBUG=false
```

## 10. Quick Reference

### Middleware Usage

```php
// Require authentication
Route::middleware('auth:sanctum')->group(function () {
    // routes
});

// Add rate limiting
Route::middleware(['auth:sanctum', 'api.rate_limit'])->group(function () {
    // routes
});

// Custom rate limit
Route::post('/login')->middleware('api.rate_limit:strict');
Route::get('/data')->middleware('api.rate_limit:relaxed');
```

### Token Abilities

```php
// Create token with abilities
$token = $user->createToken('api-token', ['server:read', 'server:write']);

// Check abilities in routes
Route::post('/servers', function () {
    //
})->middleware('ability:server:create');
```

## Summary

Phase 8 successfully implemented:
- ✅ Laravel Sanctum API authentication
- ✅ Role-based rate limiting (1000/500/100/60 req/min)
- ✅ Security headers (CSP, HSTS, X-Frame-Options, etc.)
- ✅ RESTful API endpoints (/api/v1/*)
- ✅ Token management (create, refresh, revoke)
- ✅ Rate limit response headers

Build time: N/A (backend only)
Ready for: API integration with mobile apps, SPAs, third-party services
