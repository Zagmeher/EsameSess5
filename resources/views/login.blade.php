<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>StreamFlix - Accedi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { height: 100%; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d0a0a 50%, #1a1a1a 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
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
        }
        .btn-register-nav {
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
        .btn-register-nav:hover {
            background: #dc143c;
            color: white;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
        }
        .main-container {
            position: relative;
            z-index: 1;
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .login-card {
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(220, 20, 60, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8), 0 0 40px rgba(220, 20, 60, 0.1);
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-card h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        .login-card .subtitle {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
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
        .form-control::placeholder { color: rgba(255, 255, 255, 0.4); }
        .form-control.is-invalid {
            border-color: #dc143c;
            background: rgba(220, 20, 60, 0.1);
        }
        .invalid-feedback {
            color: #dc143c;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: none;
        }
        .password-toggle { position: relative; }
        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
            transition: color 0.3s ease;
        }
        .password-toggle-icon:hover { color: #dc143c; }
        .btn-login {
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
        .btn-login:hover:not(:disabled) {
            background: linear-gradient(135deg, #ff0000 0%, #dc143c 100%);
            box-shadow: 0 6px 25px rgba(220, 20, 60, 0.6);
            transform: translateY(-2px);
        }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; }
        .register-text {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
        .register-text a {
            color: #dc143c;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .register-text a:hover {
            color: #ff0000;
            text-shadow: 0 0 10px rgba(220, 20, 60, 0.5);
        }
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
        .footer-links a:hover { color: #dc143c; }
        .footer-copyright {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            margin-top: 1rem;
        }
        .alert {
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .alert-success { border-color: #28a745; color: #28a745; }
        .alert-danger { border-color: #dc143c; color: #dc143c; }
        .spinner-border-sm { width: 1rem; height: 1rem; border-width: 0.15rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="/home">
                <i class="bi bi-play-circle-fill me-2"></i>StreamFlix
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a href="{{ url('/register') }}" class="btn btn-register-nav">
                            <i class="bi bi-person-plus me-2"></i>Registrati
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="login-card">
                        <div class="text-center mb-4">
                            <h2>
                                <i class="bi bi-box-arrow-in-right me-2" style="color: #dc143c;"></i>
                                Accedi
                            </h2>
                            <p class="subtitle">Benvenuto! Inserisci le tue credenziali</p>
                        </div>

                        <form id="loginForm">
                            @csrf
                            
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
                                <div class="invalid-feedback">Inserisci un indirizzo email valido</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                <div class="password-toggle">
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Inserisci la tua password"
                                        required
                                    >
                                    <i class="bi bi-eye password-toggle-icon" id="togglePassword"></i>
                                </div>
                                <div class="invalid-feedback">La password deve avere almeno 6 caratteri</div>
                            </div>

                            <button type="submit" class="btn btn-login">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Accedi
                            </button>
                        </form>

                        <div class="register-text">
                            Non hai un account? 
                            <a href="{{ url('/register') }}">Registrati ora</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-links">
                <a href="#"><i class="bi bi-shield-check me-1"></i>Privacy Policy</a>
                <a href="#"><i class="bi bi-file-text me-1"></i>Termini di Servizio</a>
                <a href="#"><i class="bi bi-envelope me-1"></i>Contatti</a>
            </div>
            <div class="footer-copyright">
                <p class="mb-0">© 2025 StreamFlix. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        console.log('🚀 INIZIO CARICAMENTO SCRIPT');
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOMContentLoaded - Pagina caricata');
            
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitButton = form.querySelector('button[type="submit"]');
            const togglePassword = document.getElementById('togglePassword');
            
            console.log('🔍 Elementi:', {
                form: !!form,
                emailInput: !!emailInput,
                passwordInput: !!passwordInput,
                submitButton: !!submitButton,
                togglePassword: !!togglePassword
            });

            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                console.log('👁️ Toggle password');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('bi-eye');
                    this.classList.add('bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('bi-eye-slash');
                    this.classList.add('bi-eye');
                }
            });

            // Submit form
            console.log('📝 Attacco evento submit al form...');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('🎯 SUBMIT CATTURATO!');
                
                const email = emailInput.value.trim();
                const password = passwordInput.value;
                
                console.log('📧 Email:', email);
                console.log('🔒 Password length:', password.length);
                
                // Reset errori
                emailInput.classList.remove('is-invalid');
                passwordInput.classList.remove('is-invalid');
                document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
                
                // Validazione
                let isValid = true;
                
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    console.log('❌ Email non valida');
                    emailInput.classList.add('is-invalid');
                    emailInput.parentElement.querySelector('.invalid-feedback').style.display = 'block';
                    isValid = false;
                }
                
                if (!password || password.length < 6) {
                    console.log('❌ Password troppo corta');
                    passwordInput.classList.add('is-invalid');
                    passwordInput.parentElement.querySelector('.invalid-feedback').style.display = 'block';
                    isValid = false;
                }
                
                if (!isValid) {
                    console.log('❌ Validazione fallita');
                    return;
                }
                
                // Disabilita pulsante
                const originalHtml = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Accesso in corso...';
                
                try {
                    console.log('🚀 Invio richiesta a /api/auth/login');
                    
                    const response = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email: email, password: password })
                    });
                    
                    console.log('📡 Status:', response.status);
                    
                    const result = await response.json();
                    console.log('📥 Risposta:', result);
                    
                    if (response.ok && result.success) {
                        console.log('✅ LOGIN RIUSCITO!');
                        
                        // Salva token
                        localStorage.setItem('app_access_token', result.data.access_token);
                        localStorage.setItem('app_refresh_token', result.data.refresh_token);
                        localStorage.setItem('app_token_expiry', (Date.now() + result.data.expires_in * 1000).toString());
                        
                        showAlert('success', `Benvenuto ${result.data.user.username}!`);
                        
                        setTimeout(() => {
                            console.log('🔄 Reindirizzo a /home');
                            window.location.href = '/home';
                        }, 1500);
                        
                    } else {
                        console.log('❌ LOGIN FALLITO');
                        emailInput.classList.add('is-invalid');
                        passwordInput.classList.add('is-invalid');
                        showAlert('danger', 'Utente non esistente');
                    }
                    
                } catch (error) {
                    console.error('💥 Errore:', error);
                    showAlert('danger', 'Errore di connessione. Riprova più tardi.');
                    
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalHtml;
                }
            });
            
            console.log('✅ Event listener aggiunto al form');
            
            function showAlert(type, message) {
                document.querySelectorAll('.alert').forEach(alert => alert.remove());
                
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
        
        console.log('✅ SCRIPT CARICATO COMPLETAMENTE');
    </script>
</body>
</html>
