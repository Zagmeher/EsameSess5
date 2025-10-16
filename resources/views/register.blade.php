<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>StreamFlix - Registrati</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d0a0a 50%, #1a1a1a 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(220, 20, 60, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(139, 0, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(255, 0, 0, 0.05) 0%, transparent 50%);
            z-index: 0;
            pointer-events: none;
        }

        /* Navbar personalizzata */
        .navbar-custom {
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid rgba(220, 20, 60, 0.3);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #dc143c 0%, #ff0000 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 30px rgba(220, 20, 60, 0.5);
        }

        .btn-login-nav {
            background: rgba(220, 20, 60, 0.2);
            color: #dc143c;
            border: 1px solid #dc143c;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-login-nav:hover {
            background: #dc143c;
            color: white;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
        }

        /* Container principale */
        .main-container {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        /* Card di registrazione */
        .register-card {
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(220, 20, 60, 0.2);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.8),
                0 0 40px rgba(220, 20, 60, 0.1);
            padding: 3rem;
            max-width: 500px;
            width: 100%;
        }

        .register-card h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .register-card .subtitle {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        /* Form styling */
        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            background: rgba(30, 30, 30, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 12px 20px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(40, 40, 40, 0.9);
            border-color: #dc143c;
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control.is-valid {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc143c;
            background: rgba(220, 20, 60, 0.1);
        }

        /* Select styling */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23fff' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }

        select.form-control option {
            background: #1a1a1a;
            color: white;
            padding: 10px;
        }

        .invalid-feedback {
            color: #dc143c;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .form-text {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        /* Button register */
        .btn-register {
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            width: 100%;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-register:hover:not(:disabled) {
            background: linear-gradient(135deg, #ff0000 0%, #dc143c 100%);
            box-shadow: 0 6px 25px rgba(220, 20, 60, 0.6);
            transform: translateY(-2px);
        }

        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Footer text */
        .login-text {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .login-text a {
            color: #dc143c;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-text a:hover {
            color: #ff0000;
            text-shadow: 0 0 10px rgba(220, 20, 60, 0.5);
        }

        /* Footer */
        .site-footer {
            background: rgba(0, 0, 0, 0.95);
            border-top: 2px solid rgba(220, 20, 60, 0.3);
            padding: 2rem 0 1rem;
            margin-top: auto;
            position: relative;
            z-index: 10;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #dc143c;
        }

        .footer-copyright {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            margin-top: 1rem;
        }

        /* Alert container */
        #alertContainer {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .alert {
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            border-color: #28a745;
            color: #28a745;
        }

        .alert-danger {
            border-color: #dc143c;
            color: #dc143c;
        }

        /* Animazioni */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-card {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .register-card {
                max-width: 470px;
            }
        }

        @media (max-width: 992px) {
            .register-card {
                max-width: 450px;
            }
        }

        @media (max-width: 768px) {
            .register-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .navbar-brand {
                font-size: 1.5rem;
                letter-spacing: 1px;
            }

            .register-card h2 {
                font-size: 1.75rem;
            }

            .btn-login-nav {
                padding: 6px 18px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding-top: 70px;
            }

            .navbar-custom {
                padding: 0.5rem 0;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .register-card {
                padding: 1.5rem;
                margin: 0.5rem;
                border-radius: 15px;
            }

            .register-card h2 {
                font-size: 1.5rem;
            }

            .register-card .subtitle {
                font-size: 0.85rem;
            }

            .form-control {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .form-text {
                font-size: 0.8rem;
            }

            .btn-register {
                padding: 12px;
                font-size: 0.9rem;
            }

            .btn-login-nav {
                padding: 6px 15px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 375px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .register-card {
                padding: 1.25rem;
            }

            .register-card h2 {
                font-size: 1.35rem;
            }
        }

        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="/">
                <i class="bi bi-play-circle-fill me-2"></i>StreamFlix
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#" style="color: rgba(255,255,255,0.8);">
                            <i class="bi bi-info-circle me-1"></i>Info
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#" style="color: rgba(255,255,255,0.8);">
                            <i class="bi bi-question-circle me-1"></i>Aiuto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="btn btn-login-nav">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Accedi
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Main Content -->
    <div class="main-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="register-card">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h2>
                                <i class="bi bi-person-plus-fill me-2" style="color: #dc143c;"></i>
                                Registrati
                            </h2>
                            <p class="subtitle">Crea il tuo account e inizia subito!</p>
                        </div>

                        <!-- Form -->
                        <form id="registrationForm">
                            @csrf
                            
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person me-2"></i>Username
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username" 
                                    placeholder="Scegli un username"
                                    required
                                >
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Minimo 3 caratteri, solo lettere e numeri</div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="Inserisci la tua email"
                                    required
                                >
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Crea una password"
                                    required
                                >
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Minimo 6 caratteri</div>
                            </div>
                            
                            <!-- Conferma Password -->
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">
                                    <i class="bi bi-lock-fill me-2"></i>Conferma Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="confirmPassword" 
                                    name="confirmPassword" 
                                    placeholder="Conferma la tua password"
                                    required
                                >
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Comune di Residenza -->
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

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-register">
                                <i class="bi bi-person-plus me-2"></i>
                                Crea Account
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="login-text">
                            Hai già un account? 
                            <a href="{{ url('/') }}">Accedi ora</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-links">
                <a href="#"><i class="bi bi-shield-check me-1"></i>Privacy Policy</a>
                <a href="#"><i class="bi bi-file-text me-1"></i>Termini di Servizio</a>
                <a href="#"><i class="bi bi-envelope me-1"></i>Contatti</a>
                <a href="#"><i class="bi bi-info-circle me-1"></i>Chi Siamo</a>
                <a href="#"><i class="bi bi-question-circle me-1"></i>FAQ</a>
            </div>
            <div class="footer-copyright">
                <p class="mb-0">© 2025 StreamFlix. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- VALIDAZIONE IN TEMPO REALE -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Script caricato correttamente!');
            
            const form = document.getElementById('registrationForm');
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const comuneSelect = document.getElementById('comune');
            const comuneLoadingText = document.getElementById('comuneLoadingText');
            
            console.log('Elementi trovati:', { form, usernameInput, emailInput, passwordInput, confirmPasswordInput, comuneSelect });

            /* CARICAMENTO COMUNI */
            loadComuni();

            async function loadComuni() {
                try {
                    console.log('🏙️ Caricamento comuni...');
                    const response = await fetch('/api/comuni');
                    const result = await response.json();
                    
                    if (result.success && result.data) {
                        console.log(`✅ ${result.data.length} comuni caricati`);
                        
                        // Raggruppa comuni per regione
                        const comuniPerRegione = {};
                        result.data.forEach(comune => {
                            if (!comuniPerRegione[comune.regione]) {
                                comuniPerRegione[comune.regione] = [];
                            }
                            comuniPerRegione[comune.regione].push(comune);
                        });
                        
                        // Ordina le regioni alfabeticamente
                        const regioni = Object.keys(comuniPerRegione).sort();
                        
                        // Aggiungi opzioni raggruppate per regione
                        regioni.forEach(regione => {
                            const optgroup = document.createElement('optgroup');
                            optgroup.label = regione;
                            
                            comuniPerRegione[regione].forEach(comune => {
                                const option = document.createElement('option');
                                option.value = comune.id;
                                option.textContent = `${comune.nome} (${comune.sigla_provincia}) - ${comune.cap}`;
                                optgroup.appendChild(option);
                            });
                            
                            comuneSelect.appendChild(optgroup);
                        });
                        
                        // Nascondi il testo di caricamento
                        comuneLoadingText.style.display = 'none';
                        
                    } else {
                        throw new Error('Errore nel caricamento dei comuni');
                    }
                } catch (error) {
                    console.error('❌ Errore caricamento comuni:', error);
                    comuneLoadingText.innerHTML = '<span style="color: #dc143c;">Errore nel caricamento dei comuni</span>';
                }
            }

            /* VALIDAZIONE USERNAME IN TEMPO REALE */
            usernameInput.addEventListener('input', function() {
                const value = this.value;
                console.log('Input username:', value);
                validateUsernameRealtime(value);
            });

            usernameInput.addEventListener('blur', function() {
                const value = this.value.trim();
                this.value = value;
                validateUsername(value);
            });

            function validateUsernameRealtime(value) {
                const input = usernameInput;
                const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');
                
                input.classList.remove('is-invalid', 'is-valid');
                feedbackDiv.style.display = 'none';
                feedbackDiv.textContent = '';
                
                if (!value || value.length === 0) {
                    return;
                }
                
                if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                    input.classList.add('is-invalid');
                    feedbackDiv.textContent = 'Carattere non valido';
                    feedbackDiv.style.display = 'block';
                    return;
                }
                
                if (value.length < 3) {
                    const remaining = 3 - value.length;
                    const message = `Inserisci ancora ${remaining} carattere${remaining > 1 ? 'i' : ''}`;
                    input.classList.add('is-invalid');
                    feedbackDiv.textContent = message;
                    feedbackDiv.style.display = 'block';
                    return;
                }
                
                input.classList.add('is-valid');
            }

            function validateUsername(value) {
                const input = usernameInput;
                const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');
                
                input.classList.remove('is-invalid', 'is-valid');
                feedbackDiv.style.display = 'none';
                feedbackDiv.textContent = '';
                
                if (!value || value.length === 0) {
                    input.classList.add('is-invalid');
                    feedbackDiv.textContent = 'L\'username è obbligatorio';
                    feedbackDiv.style.display = 'block';
                    return false;
                }
                
                if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                    input.classList.add('is-invalid');
                    feedbackDiv.textContent = 'Carattere non valido';
                    feedbackDiv.style.display = 'block';
                    return false;
                }
                
                if (value.length < 3) {
                    input.classList.add('is-invalid');
                    feedbackDiv.textContent = 'Username troppo corto (minimo 3 caratteri)';
                    feedbackDiv.style.display = 'block';
                    return false;
                }
                
                if (value.length > 50) {
                    input.classList.add('is-invalid');
                    feedbackDiv.textContent = 'Username troppo lungo (massimo 50 caratteri)';
                    feedbackDiv.style.display = 'block';
                    return false;
                }
                
                input.classList.add('is-valid');
                return true;
            }

            /* VALIDAZIONE PASSWORD IN TEMPO REALE */
            passwordInput.addEventListener('input', function() {
                validatePasswordRealtime(this.value);
            });

            function validatePasswordRealtime(value) {
                const input = passwordInput;
                const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');
                
                input.classList.remove('is-invalid', 'is-valid');
                feedbackDiv.style.display = 'none';
                feedbackDiv.textContent = '';
                
                if (!value || value.length === 0) {
                    return;
                }
                
                if (value.length < 6) {
                    input.classList.add('is-invalid');
                    feedbackDiv.textContent = 'Password non valida';
                    feedbackDiv.style.display = 'block';
                    return;
                }
                
                input.classList.add('is-valid');
            }

            /* SUBMIT FORM - REGISTRAZIONE UTENTE */
            const submitButton = form.querySelector('button[type="submit"]');
            const submitButtonOriginalHtml = submitButton.innerHTML;
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const username = usernameInput.value.trim();
                const email = emailInput.value.trim();
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const comuneId = comuneSelect.value;
                
                console.log('📝 Submit form - Validazione campi...');
                
                let isValid = true;
                
                // Valida username
                if (!validateUsername(username)) {
                    console.log('❌ Username non valido');
                    isValid = false;
                }
                
                // Valida comune
                const comuneFeedback = comuneSelect.parentElement.querySelector('.invalid-feedback');
                comuneSelect.classList.remove('is-invalid', 'is-valid');
                
                if (!comuneId) {
                    console.log('❌ Comune non selezionato');
                    comuneSelect.classList.add('is-invalid');
                    comuneFeedback.textContent = 'Seleziona il tuo comune di residenza';
                    comuneFeedback.style.display = 'block';
                    isValid = false;
                } else {
                    comuneSelect.classList.add('is-valid');
                }
                
                // Valida email
                const emailFeedback = emailInput.parentElement.querySelector('.invalid-feedback');
                emailInput.classList.remove('is-invalid', 'is-valid');
                
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    console.log('❌ Email non valida');
                    emailInput.classList.add('is-invalid');
                    emailFeedback.textContent = 'Email non valida';
                    emailFeedback.style.display = 'block';
                    isValid = false;
                } else {
                    emailInput.classList.add('is-valid');
                }
                
                // Valida password
                const passwordFeedback = passwordInput.parentElement.querySelector('.invalid-feedback');
                passwordInput.classList.remove('is-invalid', 'is-valid');
                
                if (!password || password.length < 6) {
                    console.log('❌ Password non valida');
                    passwordInput.classList.add('is-invalid');
                    passwordFeedback.textContent = 'Password non valida';
                    passwordFeedback.style.display = 'block';
                    isValid = false;
                } else {
                    passwordInput.classList.add('is-valid');
                }
                
                // Valida conferma password
                const confirmFeedback = confirmPasswordInput.parentElement.querySelector('.invalid-feedback');
                confirmPasswordInput.classList.remove('is-invalid', 'is-valid');
                
                if (password !== confirmPassword) {
                    console.log('❌ Le password non corrispondono');
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmFeedback.textContent = 'Le password non corrispondono';
                    confirmFeedback.style.display = 'block';
                    isValid = false;
                } else {
                    confirmPasswordInput.classList.add('is-valid');
                }
                
                if (!isValid) {
                    showAlert('danger', 'Correggi gli errori nel form');
                    return;
                }
                
                // DISABILITA PULSANTE
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrazione in corso...';
                
                try {
                    console.log('🚀 Invio dati al server...');
                    console.log('URL:', '/api/auth/register');
                    console.log('Dati:', { username, email, password: '***' });
                    
                    // CHIAMATA API
                    const response = await fetch('/api/auth/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            username: username,
                            email: email,
                            password: password,
                            comune_id: comuneId
                        })
                    });
                    
                    console.log('📡 Risposta ricevuta - Status:', response.status, response.statusText);
                    
                    const result = await response.json();
                    console.log('📥 Risposta server:', result);
                    
                    if (response.ok && result.success) {
                        console.log('✅ Utente creato con successo!');
                        console.log('👤 Dati utente:', result.data.user);
                        
                        // Salva token
                        localStorage.setItem('app_access_token', result.data.access_token);
                        localStorage.setItem('app_refresh_token', result.data.refresh_token);
                        const expiryTime = Date.now() + (result.data.expires_in * 1000);
                        localStorage.setItem('app_token_expiry', expiryTime.toString());
                        
                        showAlert('success', `Account creato con successo! Benvenuto ${result.data.user.username}!`);
                        
                        setTimeout(() => {
                            window.location.href = '/home';
                        }, 2000);
                        
                    } else {
                        console.log('❌ Errore dal server:', result);
                        
                        let errorMessage = result.message || 'Errore durante la registrazione';
                        
                        if (result.errors) {
                            if (result.errors.username) {
                                usernameInput.classList.add('is-invalid');
                                const feedback = usernameInput.parentElement.querySelector('.invalid-feedback');
                                feedback.textContent = result.errors.username[0];
                                feedback.style.display = 'block';
                                errorMessage = result.errors.username[0];
                            }
                            if (result.errors.email) {
                                emailInput.classList.add('is-invalid');
                                emailFeedback.textContent = result.errors.email[0];
                                emailFeedback.style.display = 'block';
                                errorMessage = result.errors.email[0];
                            }
                            if (result.errors.password) {
                                passwordInput.classList.add('is-invalid');
                                passwordFeedback.textContent = result.errors.password[0];
                                passwordFeedback.style.display = 'block';
                                errorMessage = result.errors.password[0];
                            }
                        }
                        
                        showAlert('danger', errorMessage);
                    }
                    
                } catch (error) {
                    console.error('💥 Errore:', error);
                    showAlert('danger', 'Errore di connessione. Riprova più tardi.');
                    
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = submitButtonOriginalHtml;
                }
            });
            
            function showAlert(type, message) {
                const existingAlerts = document.querySelectorAll('.alert');
                existingAlerts.forEach(alert => alert.remove());

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.style.position = 'fixed';
                alertDiv.style.top = '20px';
                alertDiv.style.right = '20px';
                alertDiv.style.zIndex = '9999';
                alertDiv.style.minWidth = '300px';
                alertDiv.style.maxWidth = '500px';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 150);
                }, 5000);
            }
        });
    </script>
</body>
</html>
