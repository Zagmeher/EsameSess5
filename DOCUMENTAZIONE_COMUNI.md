# 🏙️ INTEGRAZIONE COMUNI ITALIANI - DOCUMENTAZIONE

## 📋 RIEPILOGO IMPLEMENTAZIONE

### ✅ Completato con successo!

Data: 16 ottobre 2025

---

## 🗂️ STRUTTURA DATABASE

### Tabella `comuni`
```sql
CREATE TABLE comuni (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    regione VARCHAR(255) NOT NULL,
    provincia VARCHAR(255) NOT NULL,
    sigla_provincia VARCHAR(2) NOT NULL,
    codice_catastale VARCHAR(10) NOT NULL,
    cap VARCHAR(10) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_nome (nome),
    INDEX idx_regione (regione),
    INDEX idx_provincia (provincia),
    INDEX idx_sigla_provincia (sigla_provincia)
);
```

**Dati importati:** 7.981 comuni italiani da `comuniItaliani.csv`

### Tabella `users` (modificata)
Aggiunto campo:
```sql
ALTER TABLE users 
ADD COLUMN comune_id BIGINT UNSIGNED NULL AFTER password,
ADD FOREIGN KEY (comune_id) REFERENCES comuni(id) ON DELETE SET NULL;
```

---

## 🔗 RELAZIONI ELOQUENT

### Model User
```php
// app/Models/User.php

protected $fillable = [
    'username',
    'email',
    'password',
    'comune_id', // ← NUOVO
];

// Relazione: Un utente appartiene a un comune
public function comune()
{
    return $this->belongsTo(Comune::class, 'comune_id');
}
```

**Utilizzo:**
```php
$user = User::find(1);
echo $user->comune->nome; // "Roma"
echo $user->comune->provincia; // "Roma"
echo $user->comune->regione; // "Lazio"
```

### Model Comune
```php
// app/Models/Comune.php

// Relazione: Un comune ha molti utenti
public function users()
{
    return $this->hasMany(User::class, 'comune_id');
}
```

**Utilizzo:**
```php
$comune = Comune::find(1);
$utentiDelComune = $comune->users; // Tutti gli utenti di quel comune
```

---

## 🌐 API ENDPOINTS

### GET /api/comuni
Ottiene la lista di tutti i comuni italiani

**Query Parameters (opzionali):**
- `search` - Cerca per nome comune (es: `?search=Roma`)
- `regione` - Filtra per regione (es: `?regione=Lazio`)
- `provincia` - Filtra per provincia (es: `?provincia=Roma`)
- `sigla` - Filtra per sigla provincia (es: `?sigla=RM`)

**Risposta (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nome": "Roma",
            "regione": "Lazio",
            "provincia": "Roma",
            "sigla_provincia": "RM",
            "codice_catastale": "H501",
            "cap": "00100"
        }
    ]
}
```

### POST /api/auth/register
Registrazione nuovo utente CON comune di residenza

**Payload richiesto:**
```json
{
    "username": "mario_rossi",
    "email": "mario@example.com",
    "password": "password123",
    "comune_id": 1  // ← OBBLIGATORIO
}
```

**Validazione:**
- `comune_id`: obbligatorio, deve esistere nella tabella comuni

**Risposta successo (201 Created):**
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
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGci...",
        "refresh_token": "a3f8b2c1d4e5...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

---

## 🖥️ FRONTEND - Pagina di Registrazione

### Modifiche a `register.blade.php`

#### Nuovo campo nel form:
```html
<div class="mb-4">
    <label for="comune" class="form-label">
        <i class="bi bi-geo-alt me-2"></i>Comune di Residenza
    </label>
    <select 
        class="form-control" 
        id="comune" 
        name="comune_id"
        required
    >
        <option value="">Seleziona il tuo comune</option>
    </select>
    <div class="invalid-feedback"></div>
    <div class="form-text">
        <span id="comuneLoadingText">
            <span class="spinner-border spinner-border-sm me-1"></span>
            Caricamento comuni in corso...
        </span>
    </div>
</div>
```

#### Funzionalità JavaScript:
1. **Caricamento automatico comuni** all'apertura della pagina
2. **Menu a tendina organizzato per regione** (optgroup)
3. **Validazione in tempo reale** del campo comune
4. **Formato visualizzazione**: `Nome Comune (Sigla) - CAP`

**Esempio visualizzazione:**
```
Lazio
├─ Roma (RM) - 00100
├─ Fiumicino (RM) - 00054
├─ Latina (LT) - 04100
...
```

---

## 📊 STATISTICHE

### Comuni per Regione (Top 5)
1. **Lombardia**: 1.523 comuni
2. **Piemonte**: 1.201 comuni
3. **Veneto**: 575 comuni
4. **Campania**: 550 comuni
5. **Calabria**: 409 comuni

**Totale**: 7.981 comuni italiani

---

## 🧪 TEST E VERIFICA

### Script di test disponibili:

1. **`test-comuni-integration.php`**
   - Verifica completa dell'integrazione
   - Controlla tabelle, foreign key, relazioni
   - Statistiche e esempi

   ```bash
   php test-comuni-integration.php
   ```

2. **`test-comuni-model.php`**
   - Test del Model Comune
   - Esempi di query e ricerche

   ```bash
   php test-comuni-model.php
   ```

3. **`public/test-comuni-registration.html`**
   - Test della registrazione via browser
   - URL: `http://localhost/test-comuni-registration.html`

---

## 📁 FILES MODIFICATI/CREATI

### Migrations
- ✅ `2025_10_16_170331_create_comuni_table.php` - Crea tabella comuni
- ✅ `2025_10_16_170848_add_comune_id_to_users_table.php` - Aggiunge comune_id a users

### Models
- ✅ `app/Models/Comune.php` - Nuovo model
- ✅ `app/Models/User.php` - Aggiunta relazione e fillable

### Controllers
- ✅ `app/Http/Controllers/Api/AuthController.php` - Aggiunta validazione comune_id

### Routes
- ✅ `routes/web.php` - Aggiunto endpoint GET /api/comuni

### Views
- ✅ `resources/views/register.blade.php` - Aggiunto campo comune con select dinamico

### Scripts Utility
- ✅ `import-comuni.php` - Script di importazione CSV
- ✅ `verifica-comuni.php` - Verifica dati importati
- ✅ `test-comuni-model.php` - Test model Comune
- ✅ `test-comuni-integration.php` - Test integrazione completa
- ✅ `public/test-comuni-registration.html` - Test web registrazione

---

## 🎯 UTILIZZO PRATICO

### Esempio 1: Registrare un utente via JavaScript
```javascript
const response = await fetch('/api/auth/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        username: 'mario_rossi',
        email: 'mario@example.com',
        password: 'password123',
        comune_id: 35 // ID di Roma
    })
});
```

### Esempio 2: Ottenere informazioni utente con comune
```php
$user = User::with('comune')->find(1);

echo $user->username; // "mario_rossi"
echo $user->comune->nome; // "Roma"
echo $user->comune->provincia; // "Roma"
echo $user->comune->cap; // "00100"
```

### Esempio 3: Trovare tutti gli utenti di una provincia
```php
$utentiDiMilano = User::whereHas('comune', function($query) {
    $query->where('provincia', 'Milano');
})->get();
```

### Esempio 4: Cercare comuni
```php
// Per nome
$comuni = Comune::byNome('Milano')->get();

// Per regione
$comuniLazio = Comune::byRegione('Lazio')->get();

// Per sigla provincia
$comuniTO = Comune::bySiglaProvincia('TO')->get();
```

---

## ✅ CHECKLIST IMPLEMENTAZIONE

- [x] Creata tabella `comuni` con 7.981 comuni italiani
- [x] Importati tutti i dati dal CSV
- [x] Aggiunto campo `comune_id` a tabella `users`
- [x] Configurata foreign key `users.comune_id -> comuni.id`
- [x] Creato Model `Comune` con scopes di ricerca
- [x] Aggiunta relazione `User->comune()` (belongsTo)
- [x] Aggiunta relazione `Comune->users()` (hasMany)
- [x] Creato endpoint API GET `/api/comuni`
- [x] Aggiornato controller registrazione per validare `comune_id`
- [x] Modificata pagina registrazione con select comuni
- [x] Implementato caricamento dinamico comuni via JavaScript
- [x] Organizzati comuni per regione nel menu a tendina
- [x] Aggiunta validazione campo comune nel frontend
- [x] Creati script di test e verifica
- [x] Documentazione completa

---

## 🚀 PROSSIMI PASSI (Opzionali)

1. **Cache comuni**: Implementare caching per ridurre query al DB
2. **Ricerca intelligente**: Aggiungere autocomplete per ricerca comune
3. **Validazione CAP**: Validare CAP in base al comune selezionato
4. **Profilo utente**: Mostrare comune nella pagina profilo
5. **Dashboard**: Statistiche utenti per regione/provincia
6. **Export**: Funzione per esportare elenco utenti per comune

---

## 📞 SUPPORTO

Per problemi o domande:
- Verificare che le migration siano eseguite: `php artisan migrate`
- Controllare che i comuni siano importati: `php test-comuni-integration.php`
- Testare API: `curl http://localhost/api/comuni`

---

**Implementazione completata con successo! 🎉**
