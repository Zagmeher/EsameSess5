<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>StreamFlix - Accesso Confermato</title>
    
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
            text-shadow: 0 0 30px rgba(220, 20, 60, 0.5);
        }

        .btn-logout-nav {
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

        .btn-logout-nav:hover {
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

        .success-card {
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(40, 167, 69, 0.3);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.8),
                0 0 40px rgba(40, 167, 69, 0.2);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            color: #28a745;
            animation: scaleIn 0.5s ease-out;
            margin-bottom: 1.5rem;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-card h1 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .success-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .user-info {
            background: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .user-info-item:last-child {
            border-bottom: none;
        }

        .user-info-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .user-info-value {
            color: white;
            font-weight: 600;
        }

        .btn-logout {
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #ff0000 0%, #dc143c 100%);
            box-shadow: 0 6px 25px rgba(220, 20, 60, 0.6);
            transform: translateY(-2px);
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

        .footer-links a:hover {
            color: #dc143c;
        }

        .footer-copyright {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            margin-top: 1rem;
        }

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

        .success-card {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                    <li class="nav-item me-3">
                        <span class="nav-link" style="color: rgba(255,255,255,0.8);">
                            <i class="bi bi-person-circle me-1"></i>
                            <span id="navUsername">Utente</span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <button onclick="logout()" class="btn btn-logout-nav">
                            <i class="bi bi-box-arrow-right me-2"></i>Esci
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="success-card">
                        <!-- Icona di successo -->
                        <div class="success-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                        <!-- Titolo -->
                        <h1>Accesso Confermato</h1>
                        <p>Benvenuto! Hai effettuato l'accesso con successo.</p>

                        <!-- Informazioni utente -->
                        <div class="user-info">
                            <div class="user-info-item">
                                <span class="user-info-label">
                                    <i class="bi bi-person me-2"></i>Username
                                </span>
                                <span class="user-info-value" id="username">Caricamento...</span>
                            </div>
                            <div class="user-info-item">
                                <span class="user-info-label">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </span>
                                <span class="user-info-value" id="email">Caricamento...</span>
                            </div>
                            <div class="user-info-item">
                                <span class="user-info-label">
                                    <i class="bi bi-calendar me-2"></i>Registrato il
                                </span>
                                <span class="user-info-value" id="created_at">Caricamento...</span>
                            </div>
                        </div>

                        <!-- Pulsante logout -->
                        <button onclick="logout()" class="btn-logout">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Esci dall'Account
                        </button>
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
    
    <!-- Script per caricare dati utente -->
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('✅ Dashboard caricata');
            
            // Recupera token dal localStorage
            const token = localStorage.getItem('app_access_token');
            
            if (!token) {
                console.log('❌ Nessun token trovato, reindirizzo al login');
                window.location.href = '/';
                return;
            }
            
            try {
                console.log('🔑 Token trovato, carico dati utente...');
                
                // Chiamata API per ottenere dati utente autenticato
                const response = await fetch('/api/auth/me', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                console.log('📡 Risposta API /me - Status:', response.status);
                
                if (!response.ok) {
                    throw new Error('Token non valido o scaduto');
                }
                
                const result = await response.json();
                console.log('📥 Dati utente ricevuti:', result);
                
                if (result.success && result.data) {
                    const user = result.data;
                    
                    // Aggiorna interfaccia con dati utente
                    document.getElementById('username').textContent = user.username;
                    document.getElementById('email').textContent = user.email;
                    document.getElementById('navUsername').textContent = user.username;
                    
                    // Formatta data di registrazione
                    if (user.created_at) {
                        const date = new Date(user.created_at);
                        const formattedDate = date.toLocaleDateString('it-IT', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                        document.getElementById('created_at').textContent = formattedDate;
                    }
                    
                    console.log('✅ Dati utente caricati con successo');
                } else {
                    throw new Error('Formato risposta non valido');
                }
                
            } catch (error) {
                console.error('💥 Errore caricamento dati:', error);
                
                // Se c'è errore, cancella token e reindirizza al login
                localStorage.removeItem('app_access_token');
                localStorage.removeItem('app_refresh_token');
                localStorage.removeItem('app_token_expiry');
                
                alert('Sessione scaduta. Effettua nuovamente il login.');
                window.location.href = '/';
            }
        });
        
        /* Funzione di logout */
        function logout() {
            console.log('🚪 Logout in corso...');
            
            // Recupera token
            const token = localStorage.getItem('app_access_token');
            
            // Cancella token dal localStorage
            localStorage.removeItem('app_access_token');
            localStorage.removeItem('app_refresh_token');
            localStorage.removeItem('app_token_expiry');
            
            // Chiama API logout (opzionale, per invalidare token sul server)
            if (token) {
                fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    console.log('✅ Logout server completato');
                }).catch(err => {
                    console.log('⚠️ Errore logout server:', err);
                });
            }
            
            // Reindirizza al login
            window.location.href = '/';
        }
    </script>
</body>
</html>
