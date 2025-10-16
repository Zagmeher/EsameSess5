# TEST REGISTRAZIONE UTENTE CON JWT

## 🔐 FLUSSO COMPLETO DI REGISTRAZIONE

### 1️⃣ **FRONTEND (register.blade.php)**
Quando l'utente compila il form e clicca "Crea Account":

```javascript
// Validazione lato client
- Username: min 3 caratteri, solo a-z A-Z 0-9 _
- Email: formato valido
- Password: min 6 caratteri
- Conferma password: deve corrispondere

// Chiamata API
AuthService.register({
    username: 'mario_rossi',
    email: 'mario@example.com',
    password: 'password123'
})
```

### 2️⃣ **BACKEND (AuthController@register)**
Route: `POST /api/auth/register`

#### FASE 1: Validazione Server
```php
Validator::make($request->all(), [
    'username' => 'required|string|min:3|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
    'email' => 'required|string|email|max:100|unique:users,email',
    'password' => 'required|string|min:6|max:100',
]);
```

#### FASE 2: Creazione Utente nel Database
```php
$user = User::create([
    'username' => $request->username,
    'email' => $request->email,
    'password' => Hash::make($request->password), // 🔒 BCRYPT HASH
]);
```

**QUERY SQL ESEGUITA:**
```sql
INSERT INTO users (username, email, password, created_at, updated_at)
VALUES (
    'mario_rossi',
    'mario@example.com',
    '$2y$12$...' /* Password crittografata con bcrypt */,
    NOW(),
    NOW()
);
```

#### FASE 3: Generazione Token JWT
```php
// Access Token JWT (valido 1 ora)
$token = JWTAuth::fromUser($user);

// Refresh Token (salvato in DB con hash SHA-256)
$refreshToken = RefreshToken::generateForUser(
    $user->id,
    $request->ip(),
    $request->userAgent()
);
```

**QUERY SQL ESEGUITA:**
```sql
INSERT INTO refresh_tokens (user_id, token, expires_at, ip_address, user_agent, created_at, updated_at)
VALUES (
    1,
    /* Hash SHA-256 del refresh token */
    '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08',
    DATE_ADD(NOW(), INTERVAL 30 DAY),
    '127.0.0.1',
    'Mozilla/5.0...',
    NOW(),
    NOW()
);
```

#### FASE 4: Risposta JSON
```json
{
    "success": true,
    "message": "Registrazione completata con successo!",
    "data": {
        "user": {
            "id": 1,
            "username": "mario_rossi",
            "email": "mario@example.com"
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "refresh_token": "64_caratteri_random...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

### 3️⃣ **FRONTEND - Salvataggio Token**
```javascript
// TokenService salva in localStorage
localStorage.setItem('app_access_token', 'eyJ0eXAiOiJKV1QiLCJhbGc...');
localStorage.setItem('app_refresh_token', '64_caratteri_random...');
localStorage.setItem('app_token_expiry', '1729012345678');
```

### 4️⃣ **REINDIRIZZAMENTO**
```javascript
window.location.href = '/home';
```

---

## 🔒 SICUREZZA IMPLEMENTATA

### Password Crittografata
- ✅ **Algoritmo**: Bcrypt (Hash irreversibile)
- ✅ **Salt**: Generato automaticamente da Laravel
- ✅ **Cost Factor**: 12 (configurabile in config/hashing.php)
- ❌ **MAI** salvata in chiaro nel database

**Esempio hash bcrypt:**
```
Password originale: password123
Hash salvato: $2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TuWKdv9KyA5zYx1PvLdGz8x2/3Km
```

### JWT Token
- ✅ **Header**: Algoritmo e tipo (HS256, JWT)
- ✅ **Payload**: user_id, username, email, iat, exp
- ✅ **Signature**: HMAC SHA-256 con secret key
- ✅ **Validità**: 1 ora (3600 secondi)

**Struttura JWT:**
```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9  <- Header
.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0...  <- Payload (Base64)
.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV  <- Signature
```

### Refresh Token
- ✅ **Lunghezza**: 64 caratteri random
- ✅ **Hash**: SHA-256 nel database
- ✅ **Scadenza**: 30 giorni
- ✅ **Token Rotation**: Vecchio revocato dopo uso
- ✅ **Tracking**: IP e User-Agent salvati

---

## 🧪 COME TESTARE

### Test 1: Registrazione Completa
1. Vai su: http://127.0.0.1:8000/register
2. Compila il form:
   - Username: `test_user`
   - Email: `test@example.com`
   - Password: `password123`
   - Conferma: `password123`
3. Clicca "Crea Account"
4. Verifica:
   - ✅ Alert verde "Account creato con successo!"
   - ✅ Reindirizzamento a /home
   - ✅ Console: "Token salvati"

### Test 2: Verifica Database
```sql
-- Controlla utente creato
SELECT id, username, email, password, created_at 
FROM users 
WHERE email = 'test@example.com';

-- Verifica password hashata (inizia con $2y$)
-- Output: $2y$12$...

-- Controlla refresh token
SELECT user_id, LEFT(token, 20) as token_hash, expires_at, ip_address 
FROM refresh_tokens 
WHERE user_id = (SELECT id FROM users WHERE email = 'test@example.com');
```

### Test 3: Verifica Token JWT
1. Apri Developer Tools (F12)
2. Console → Copia l'access_token dal log
3. Vai su: https://jwt.io/
4. Incolla il token
5. Verifica payload contiene:
   ```json
   {
     "sub": "1",
     "username": "test_user",
     "email": "test@example.com",
     "iat": 1729008000,
     "exp": 1729011600
   }
   ```

### Test 4: Verifica LocalStorage
1. Developer Tools (F12)
2. Application → Local Storage → http://127.0.0.1:8000
3. Verifica chiavi:
   - `app_access_token`: Presente (JWT lungo)
   - `app_refresh_token`: Presente (64 caratteri)
   - `app_token_expiry`: Presente (timestamp)

---

## 🚨 VALIDAZIONI CHE BLOCCANO LA REGISTRAZIONE

### Lato Client (JavaScript)
- ❌ Username con caratteri speciali → "Carattere non valido"
- ❌ Username < 3 caratteri → "Username troppo corto"
- ❌ Password < 6 caratteri → "Password non valida"
- ❌ Email formato errato → "Email non valida"
- ❌ Password ≠ Conferma → "Le password non corrispondono"

### Lato Server (Laravel)
- ❌ Username già esistente → HTTP 422: "Questo username è già in uso"
- ❌ Email già registrata → HTTP 422: "Questa email è già registrata"
- ❌ Validazione regex fallita → HTTP 422: "Carattere non valido"

---

## 📊 STATO ATTUALE SISTEMA

✅ **Database**: Tabelle `users` e `refresh_tokens` create
✅ **Backend**: AuthController con validazione e crittografia
✅ **JWT**: Configurato con secret key
✅ **Frontend**: Validazione real-time + chiamata API
✅ **TokenService**: Gestione localStorage
✅ **AuthService**: Auto-refresh e interceptor

**Sistema PRONTO per la registrazione completa con JWT!** 🎉
