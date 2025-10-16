<!-- 
    ============================================
    PAGINA DI LOGIN - StreamFlix
    ============================================
    Questa pagina gestisce l'accesso degli utenti al sistema.
    Utilizza Bootstrap 5.3.0 per il layout responsive e gli stili.
    Design ispirato ai servizi di streaming video (Netflix, Prime Video).
    ============================================
-->
<!DOCTYPE html>
<html lang="it">
<head>
    <!-- Codifica caratteri UTF-8 per supporto caratteri speciali -->
    <meta charset="UTF-8">
    <!-- Configurazione viewport per design responsive su tutti i dispositivi -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Token CSRF di Laravel per protezione contro attacchi Cross-Site Request Forgery -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Titolo della pagina visualizzato nel tab del browser -->
    <title>StreamFlix - Accedi</title>
    
    <!-- Framework Bootstrap 5.3.0 per componenti UI e grid responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Libreria icone Bootstrap per simboli grafici -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font personalizzato Google Fonts - Poppins con vari pesi -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ========================================
           STILI GENERALI E RESET CSS
           ======================================== */
        
        /* Reset margini, padding e box-sizing per tutti gli elementi */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Altezza 100% per l'elemento html */
        html {
            height: 100%;
        }

        /* Stili del body principale */
        body {
            font-family: 'Poppins', sans-serif; /* Font personalizzato */
            background: linear-gradient(135deg, #1a1a1a 0%, #2d0a0a 50%, #1a1a1a 100%); /* Gradiente nero-rosso scuro */
            min-height: 100vh; /* Altezza minima 100% viewport */
            position: relative;
            overflow-x: hidden; /* Nasconde scroll orizzontale */
            display: flex;
            flex-direction: column; /* Layout verticale per header, content, footer */
        }

        /* Effetto pattern di sfondo decorativo con gradienti radiali */
        body::before {
            content: '';
            position: fixed; /* Fisso durante lo scroll */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Tre gradienti radiali sovrapposti per effetto profondità */
            background: 
                radial-gradient(circle at 20% 50%, rgba(220, 20, 60, 0.1) 0%, transparent 50%), /* Rosso cremisi a sinistra */
                radial-gradient(circle at 80% 80%, rgba(139, 0, 0, 0.1) 0%, transparent 50%), /* Rosso scuro in basso a destra */
                radial-gradient(circle at 40% 20%, rgba(255, 0, 0, 0.05) 0%, transparent 50%); /* Rosso chiaro in alto */
            z-index: 0; /* Sotto tutti gli altri elementi */
            pointer-events: none; /* Non intercetta eventi del mouse */
        }

        /* ========================================
           STILI NAVBAR (BARRA DI NAVIGAZIONE)
           ======================================== */
        
        /* Stili personalizzati per la navbar superiore */
        .navbar-custom {
            background: rgba(0, 0, 0, 0.95); /* Sfondo nero semi-trasparente */
            backdrop-filter: blur(10px); /* Effetto blur dello sfondo sottostante */
            border-bottom: 2px solid rgba(220, 20, 60, 0.3); /* Bordo inferiore rosso sottile */
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); /* Ombra per profondità */
        }

        /* Stili per il logo/brand del sito */
        .navbar-brand {
            font-size: 2rem; /* Dimensione testo grande */
            font-weight: 700; /* Grassetto */
            background: linear-gradient(135deg, #dc143c 0%, #ff0000 100%); /* Gradiente rosso */
            -webkit-background-clip: text; /* Applica gradiente al testo (Webkit) */
            -webkit-text-fill-color: transparent; /* Rende testo trasparente per mostrare gradiente */
            background-clip: text; /* Standard */
            text-transform: uppercase; /* Testo maiuscolo */
            letter-spacing: 2px; /* Spaziatura tra lettere */
            text-shadow: 0 0 30px rgba(220, 20, 60, 0.5); /* Bagliore rosso */
        }

        /* Effetto hover sul logo */
        .navbar-brand:hover {
            filter: brightness(1.2); /* Aumenta luminosità */
            transform: scale(1.05); /* Ingrandimento leggero */
            transition: all 0.3s ease; /* Transizione fluida */
        }

        /* Pulsante "Registrati" nella navbar */
        .btn-register {
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%); /* Gradiente rosso-rosso scuro */
            color: white;
            border: none;
            padding: 10px 30px; /* Spaziatura interna */
            border-radius: 50px; /* Bordi molto arrotondati (pill shape) */
            font-weight: 600; /* Semi-grassetto */
            text-transform: uppercase; /* Testo maiuscolo */
            letter-spacing: 1px; /* Spaziatura lettere */
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4); /* Ombra rossa */
            transition: all 0.3s ease; /* Transizione per animazioni */
        }

        /* Effetto hover sul pulsante registrati */
        .btn-register:hover {
            background: linear-gradient(135deg, #ff0000 0%, #dc143c 100%); /* Gradiente invertito */
            box-shadow: 0 6px 25px rgba(220, 20, 60, 0.6); /* Ombra più marcata */
            transform: translateY(-2px); /* Sollevamento */
            color: white; /* Mantiene colore bianco */
        }

        /* ========================================
           CONTAINER PRINCIPALE
           ======================================== */
        
        /* Container che contiene la card di login - centrato verticalmente */
        .main-container {
            position: relative;
            z-index: 1; /* Sopra lo sfondo decorativo */
            min-height: calc(100vh - 100px); /* Altezza meno navbar e footer */
            display: flex;
            align-items: center; /* Centratura verticale */
            justify-content: center; /* Centratura orizzontale */
            padding: 2rem 0;
        }

        /* ========================================
           CARD DI LOGIN (COMPONENTE PRINCIPALE)
           ======================================== */
        
        /* Stili per la card contenente il form di login */
        .login-card {
            background: rgba(20, 20, 20, 0.95); /* Sfondo nero semi-trasparente */
            backdrop-filter: blur(20px); /* Effetto vetro smerigliato */
            border-radius: 20px; /* Bordi arrotondati */
            border: 1px solid rgba(220, 20, 60, 0.2); /* Bordo rosso sottile */
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.8), /* Ombra nera profonda */
                0 0 40px rgba(220, 20, 60, 0.1); /* Bagliore rosso esterno */
            padding: 3rem; /* Spaziatura interna generosa */
            max-width: 450px; /* Larghezza massima */
            width: 100%; /* Larghezza piena fino al massimo */
        }

        /* Titolo della card (h2) */
        .login-card h2 {
            color: white; /* Testo bianco */
            font-weight: 700; /* Grassetto */
            margin-bottom: 0.5rem; /* Margine inferiore */
            font-size: 2rem; /* Dimensione grande */
        }

        /* Sottotitolo della card */
        .login-card .subtitle {
            color: rgba(255, 255, 255, 0.6); /* Grigio chiaro */
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        /* ========================================
           STILI FORM (CAMPI INPUT)
           ======================================== */
        
        /* Label dei campi form */
        .form-label {
            color: rgba(255, 255, 255, 0.9); /* Bianco quasi opaco */
            font-weight: 500; /* Medio peso */
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        /* Stili per i campi input (email, password) */
        .form-control {
            background: rgba(30, 30, 30, 0.8); /* Grigio scuro semi-trasparente */
            border: 1px solid rgba(255, 255, 255, 0.1); /* Bordo grigio sottile */
            border-radius: 10px; /* Bordi arrotondati */
            padding: 12px 20px; /* Spaziatura interna */
            color: white; /* Testo bianco */
            font-size: 0.95rem;
            transition: all 0.3s ease; /* Transizione per effetti hover/focus */
        }

        /* Effetto quando il campo è in focus (clic su input) */
        .form-control:focus {
            background: rgba(40, 40, 40, 0.9); /* Sfondo leggermente più chiaro */
            border-color: #dc143c; /* Bordo rosso cremisi */
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25); /* Alone rosso esterno */
            color: white; /* Mantiene testo bianco */
        }

        /* Stile per il testo placeholder nei campi */
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4); /* Grigio chiaro semi-trasparente */
        }

        /* ========================================
           CAMPO PASSWORD CON ICONA MOSTRA/NASCONDI
           ======================================== */
        
        /* Container per il campo password con icona toggle */
        .password-toggle {
            position: relative; /* Posizionamento relativo per icona assoluta */
        }

        /* Icona per mostrare/nascondere password */
        .password-toggle .toggle-icon {
            position: absolute; /* Posizionamento assoluto dentro il container */
            right: 15px; /* Distanza dal bordo destro */
            top: 50%; /* Centratura verticale */
            transform: translateY(-50%); /* Correzione centratura */
            color: rgba(255, 255, 255, 0.5); /* Grigio chiaro */
            cursor: pointer; /* Cursore a manina */
            transition: color 0.3s ease; /* Transizione colore */
        }

        /* Effetto hover sull'icona toggle */
        .password-toggle .toggle-icon:hover {
            color: #dc143c; /* Diventa rosso */
        }

        /* ========================================
           CHECKBOX PERSONALIZZATA
           ======================================== */
        
        /* Stili per la checkbox "Ricordami" */
        .form-check-input {
            background-color: rgba(30, 30, 30, 0.8); /* Sfondo scuro */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Bordo grigio */
        }

        /* Checkbox quando è selezionata */
        .form-check-input:checked {
            background-color: #dc143c; /* Sfondo rosso */
            border-color: #dc143c; /* Bordo rosso */
        }

        /* Label della checkbox */
        .form-check-label {
            color: rgba(255, 255, 255, 0.8); /* Testo grigio chiaro */
            font-size: 0.9rem;
        }

        /* ========================================
           LINK E PULSANTI
           ======================================== */
        
        /* Link "Password dimenticata?" */
        .forgot-password {
            color: #dc143c; /* Rosso cremisi */
            text-decoration: none; /* Nessuna sottolineatura */
            font-size: 0.9rem;
            font-weight: 500; /* Peso medio */
            transition: all 0.3s ease; /* Transizione per hover */
        }

        /* Effetto hover sul link password dimenticata */
        .forgot-password:hover {
            color: #ff0000; /* Rosso più brillante */
            text-shadow: 0 0 10px rgba(220, 20, 60, 0.5); /* Bagliore rosso */
        }

        /* Pulsante principale "Accedi" */
        .btn-login {
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%); /* Gradiente rosso */
            color: white;
            border: none;
            padding: 14px; /* Spaziatura generosa */
            border-radius: 10px; /* Bordi arrotondati */
            font-weight: 600; /* Semi-grassetto */
            text-transform: uppercase; /* Testo maiuscolo */
            letter-spacing: 1.5px; /* Spaziatura lettere */
            width: 100%; /* Larghezza piena */
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4); /* Ombra rossa */
            transition: all 0.3s ease; /* Transizione fluida */
            margin-top: 1rem;
        }

        /* Effetto hover sul pulsante accedi */
        .btn-login:hover {
            background: linear-gradient(135deg, #ff0000 0%, #dc143c 100%); /* Gradiente invertito */
            box-shadow: 0 6px 25px rgba(220, 20, 60, 0.6); /* Ombra più pronunciata */
            transform: translateY(-2px); /* Sollevamento */
        }

        /* Effetto clic sul pulsante accedi */
        .btn-login:active {
            transform: translateY(0); /* Ritorna alla posizione originale */
        }

        /* ========================================
           DIVISORE TRA SEZIONI
           ======================================== */
        
        /* Linea divisoria con testo centrale "oppure" */
        .divider {
            display: flex;
            align-items: center; /* Centratura verticale */
            text-align: center;
            margin: 2rem 0; /* Margini verticali */
        }

        /* Linee prima e dopo il testo del divisore */
        .divider::before,
        .divider::after {
            content: ''; /* Necessario per pseudo-elementi */
            flex: 1; /* Occupa spazio disponibile */
            border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* Linea grigia sottile */
        }

        /* Testo centrale del divisore */
        .divider span {
            color: rgba(255, 255, 255, 0.5); /* Grigio chiaro */
            padding: 0 1rem; /* Spaziatura laterale */
            font-size: 0.85rem;
        }

        /* ========================================
           PULSANTI SOCIAL LOGIN (NON UTILIZZATI)
           ======================================== */
        
        /* Stili per pulsanti social (Google, Facebook) - attualmente non presenti nel form */
        .btn-social {
            background: rgba(30, 30, 30, 0.8); /* Sfondo scuro */
            border: 1px solid rgba(255, 255, 255, 0.1); /* Bordo grigio */
            color: white;
            padding: 12px;
            border-radius: 10px;
            width: 100%; /* Larghezza piena */
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 0.75rem; /* Spazio tra pulsanti */
        }

        /* Effetto hover sui pulsanti social */
        .btn-social:hover {
            background: rgba(40, 40, 40, 0.9); /* Sfondo più chiaro */
            border-color: rgba(220, 20, 60, 0.5); /* Bordo rosso */
            color: white;
            transform: translateY(-2px); /* Sollevamento */
        }

        /* Icone nei pulsanti social */
        .btn-social i {
            margin-right: 10px; /* Spazio tra icona e testo */
            font-size: 1.1rem; /* Dimensione icona */
        }

        /* Footer text */
        .signup-text {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1); /* Bordo superiore */
            color: rgba(255, 255, 255, 0.6); /* Testo grigio */
            font-size: 0.9rem;
        }

        /* Link nel testo di registrazione */
        .signup-text a {
            color: #dc143c; /* Rosso */
            text-decoration: none; /* Nessuna sottolineatura */
            font-weight: 600; /* Semi-grassetto */
            transition: all 0.3s ease;
        }

        /* Effetto hover sul link registrazione */
        .signup-text a:hover {
            color: #ff0000; /* Rosso brillante */
            text-shadow: 0 0 10px rgba(220, 20, 60, 0.5); /* Bagliore */
        }

        /* ========================================
           FOOTER DEL SITO
           ======================================== */
        
        /* Container principale del footer */
        .site-footer {
            background: rgba(0, 0, 0, 0.95); /* Sfondo nero quasi opaco */
            border-top: 2px solid rgba(220, 20, 60, 0.3); /* Bordo superiore rosso */
            padding: 2rem 0 1rem;
            margin-top: auto; /* Spinge il footer in basso */
            position: relative;
            z-index: 10; /* Sopra gli altri elementi */
        }

        /* Container per i link del footer */
        .footer-links {
            display: flex;
            justify-content: center; /* Centratura orizzontale */
            gap: 2rem; /* Spazio tra i link */
            margin-bottom: 1rem;
            flex-wrap: wrap; /* Va a capo su schermi piccoli */
        }

        /* Stili per i singoli link del footer */
        .footer-links a {
            color: rgba(255, 255, 255, 0.7); /* Grigio chiaro */
            text-decoration: none; /* Nessuna sottolineatura */
            font-size: 0.9rem;
            transition: all 0.3s ease; /* Transizione per hover */
        }

        /* Effetto hover sui link del footer */
        .footer-links a:hover {
            color: #dc143c; /* Diventa rosso */
        }

        /* Testo copyright nel footer */
        .footer-copyright {
            text-align: center; /* Testo centrato */
            color: rgba(255, 255, 255, 0.5); /* Grigio semi-trasparente */
            font-size: 0.85rem;
            margin-top: 1rem;
        }

        /* ========================================
           CONTAINER PER ALERT/NOTIFICHE
           ======================================== */
        
        /* Container fisso per messaggi di errore/successo */
        #alertContainer {
            position: fixed; /* Fisso nella viewport */
            top: 100px; /* Distanza dall'alto */
            right: 20px; /* Distanza da destra */
            z-index: 9999; /* Sopra tutti gli elementi */
            max-width: 400px; /* Larghezza massima alert */
        }

        /* ========================================
           ANIMAZIONI CSS
           ======================================== */
        
        /* Animazione di entrata dal basso con fade in */
        @keyframes fadeInUp {
            from {
                opacity: 0; /* Invisibile */
                transform: translateY(30px); /* Spostato in basso */
            }
            to {
                opacity: 1; /* Completamente visibile */
                transform: translateY(0); /* Posizione normale */
            }
        }

        /* Applicazione animazione alla card di login */
        .login-card {
            animation: fadeInUp 0.6s ease-out; /* Durata 0.6 secondi */
        }

        /* ========================================
           MEDIA QUERIES - RESPONSIVE DESIGN
           ======================================== */
        
        /* Schermi larghi (desktop grandi) - max 1200px */
        @media (max-width: 1200px) {
            .login-card {
                max-width: 420px;
            }
        }

        @media (max-width: 992px) {
            .login-card {
                max-width: 400px;
            }
        }

        @media (max-width: 768px) {
            .login-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            /* Riduzione dimensione logo */
            .navbar-brand {
                font-size: 1.5rem;
                letter-spacing: 1px;
            }

            /* Riduzione dimensione titolo card */
            .login-card h2 {
                font-size: 1.75rem;
            }

            /* Riduzione dimensione pulsante registrati */
            .btn-register {
                padding: 8px 20px;
                font-size: 0.9rem;
            }
        }

        /* Tablet piccoli - max 576px */
        @media (max-width: 576px) {
            body {
                padding-top: 70px; /* Spazio per navbar più piccola */
            }

            /* Navbar più compatta */
            .navbar-custom {
                padding: 0.5rem 0;
            }

            /* Logo ancora più piccolo */
            .navbar-brand {
                font-size: 1.3rem;
            }

            /* Card più compatta con margini ridotti */
            .login-card {
                padding: 1.5rem; /* Padding ridotto */
                margin: 0.5rem; /* Margini laterali ridotti */
                border-radius: 15px; /* Bordi meno arrotondati */
            }

            /* Titolo più piccolo */
            .login-card h2 {
                font-size: 1.5rem;
            }

            /* Sottotitolo più piccolo */
            .login-card .subtitle {
                font-size: 0.85rem;
            }

            /* Campi input più compatti */
            .form-control {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            /* Pulsante login più compatto */
            .btn-login {
                padding: 12px;
                font-size: 0.9rem;
            }

            /* Pulsanti social più compatti */
            .btn-social {
                padding: 10px;
                font-size: 0.9rem;
            }

            /* Pulsante registrati più piccolo */
            .btn-register {
                padding: 6px 15px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 375px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .login-card {
                padding: 1.25rem;
            }

            .login-card h2 {
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
                <!-- Lista di navigazione allineata a destra -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Link Info -->
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#" style="color: rgba(255,255,255,0.8);">
                            <i class="bi bi-info-circle me-1"></i>Info
                        </a>
                    </li>
                    <!-- Link Aiuto -->
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#" style="color: rgba(255,255,255,0.8);">
                            <i class="bi bi-question-circle me-1"></i>Aiuto
                        </a>
                    </li>
                    <!-- Pulsante Registrati -->
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-register">
                            <i class="bi bi-person-plus me-2"></i>Registrati
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============================================
         CONTAINER PER MESSAGGI DI ALERT
         ============================================
         Container fisso per mostrare messaggi di successo/errore
    -->
    <div id="alertContainer"></div>

    <!-- ============================================
         CONTENUTO PRINCIPALE DELLA PAGINA
         ============================================ -->
    <div class="main-container">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Colonna centrata con larghezze responsive -->
                <div class="col-lg-5 col-md-7">
                    <!-- CARD DI LOGIN -->
                    <div class="login-card">
                        <!-- Intestazione card con titolo e sottotitolo -->
                        <div class="text-center mb-4">
                            <h2>
                                <i class="bi bi-box-arrow-in-right me-2" style="color: #dc143c;"></i>
                                Accedi
                            </h2>
                            <p class="subtitle">Bentornato! Accedi al tuo account</p>
                        </div>

                        <!-- FORM DI LOGIN -->
                        <form id="loginForm">
                            <!-- Token CSRF Laravel per sicurezza form -->
                            @csrf
                            
                            <!-- CAMPO EMAIL -->
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

                            <!-- CAMPO PASSWORD -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                <!-- Container con icona mostra/nascondi password -->
                                <div class="password-toggle">
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Inserisci la tua password"
                                        required
                                    >
                                    <!-- Icona per toggleare visibilità password -->
                                    <i class="bi bi-eye toggle-icon" id="togglePassword"></i>
                                </div>
                                <!-- Container per messaggi di errore validazione -->
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- RIGA CON CHECKBOX "RICORDAMI" E LINK "PASSWORD DIMENTICATA" -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <!-- Checkbox Ricordami -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Ricordami
                                    </label>
                                </div>
                                <!-- Link per recupero password -->
                                <a href="#" class="forgot-password">Password dimenticata?</a>
                            </div>

                            <!-- PULSANTE DI SUBMIT DEL FORM -->
                            <button type="submit" class="btn btn-login">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Accedi
                            </button>
                        </form>

                        <!-- LINK PER REGISTRAZIONE NUOVO UTENTE -->
                        <div class="signup-text">
                            Non hai ancora un account? 
                            <a href="{{ route('register') }}">Registrati ora</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         FOOTER DEL SITO
         ============================================ -->
    <footer class="site-footer">
        <div class="container">
            <!-- Lista link del footer con icone -->
            <div class="footer-links">
                <a href="#"><i class="bi bi-shield-check me-1"></i>Privacy Policy</a>
                <a href="#"><i class="bi bi-file-text me-1"></i>Termini di Servizio</a>
                <a href="#"><i class="bi bi-envelope me-1"></i>Contatti</a>
                <a href="#"><i class="bi bi-info-circle me-1"></i>Chi Siamo</a>
                <a href="#"><i class="bi bi-question-circle me-1"></i>FAQ</a>
            </div>
            <!-- Copyright -->
            <div class="footer-copyright">
                <p class="mb-0">© 2025 StreamFlix. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <!-- ============================================
         INCLUSIONE JAVASCRIPT
         ============================================ -->
    
    <!-- Libreria Bootstrap JavaScript per componenti interattivi -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- APPLICAZIONE TYPESCRIPT COMPILATA (include AuthService e TokenService) -->
    <script type="module" src="{{ asset('js/app.js') }}"></script>
    
    <script type="module">
        /* ============================================
           IMPORTAZIONE MODULI TYPESCRIPT
           ============================================
           Importa AuthService per gestire login con JWT
        */
        import { AuthService } from '/js/services/AuthService.js';
        import { TokenService } from '/js/services/TokenService.js';

        /* ============================================
           CONTROLLO SESSIONE ALL'AVVIO
           ============================================
           Verifica se l'utente è già autenticato
           quando carica la pagina di login
        */
        document.addEventListener('DOMContentLoaded', async function() {
            // Se utente già autenticato, reindirizza alla dashboard
            if (AuthService.isAuthenticated()) {
                console.log('Utente già autenticato, reindirizzamento...');
                // TODO: Sostituire con URL dashboard quando sarà creata
                // window.location.href = '/dashboard';
                
                // Per ora mostra alert con dati utente
                try {
                    const user = await AuthService.getAuthenticatedUser();
                    showAlert('info', `Benvenuto ${user.username}! Sei già autenticato.`);
                } catch (error) {
                    console.error('Errore recupero utente:', error);
                }
            }
        });

        /* ============================================
           FUNZIONALITÀ MOSTRA/NASCONDI PASSWORD
           ============================================
           Permette all'utente di visualizzare temporaneamente
           la password cliccando sull'icona dell'occhio
        */
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password'); // Campo password
            const icon = this; // Icona cliccata
            
            // Toggle tra tipo "password" (nascosto) e "text" (visibile)
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text'; // Mostra password
                icon.classList.remove('bi-eye'); // Rimuove icona occhio
                icon.classList.add('bi-eye-slash'); // Aggiunge icona occhio sbarrato
            } else {
                passwordInput.type = 'password'; // Nascondi password
                icon.classList.remove('bi-eye-slash'); // Rimuove icona sbarrata
                icon.classList.add('bi-eye'); // Ripristina icona occhio
            }
        });

        /* ============================================
           GESTIONE SUBMIT DEL FORM LOGIN CON JWT
           ============================================
           Gestisce l'invio del form di login utilizzando
           AuthService per autenticazione con JWT
        */
        const loginForm = document.getElementById('loginForm');
        const submitButton = loginForm.querySelector('button[type="submit"]');
        const submitButtonText = submitButton.innerHTML;

        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Previene il submit standard del form
            
            // Recupera valori dai campi input
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            // FASE 1: VALIDAZIONE LATO CLIENT
            // Verifica che i campi non siano vuoti
            if (!email || !password) {
                showAlert('danger', 'Inserisci email e password');
                return;
            }

            // Validazione formato email basilare
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('danger', 'Inserisci un indirizzo email valido');
                return;
            }

            // FASE 2: DISABILITAZIONE UI DURANTE RICHIESTA
            // Disabilita pulsante e mostra spinner per feedback visivo
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Accesso in corso...';
            
            // Rimuove messaggi di errore precedenti
            clearFieldErrors();

            try {
                // FASE 3: CHIAMATA API LOGIN CON JWT
                console.log('Tentativo login per:', email);
                
                // Effettua login tramite AuthService
                // AuthService gestisce automaticamente:
                // - Invio credenziali al server
                // - Ricezione JWT e refresh token
                // - Salvataggio token in localStorage
                const authResponse = await AuthService.login({
                    email: email,
                    password: password
                });

                console.log('Login riuscito:', authResponse);

                // FASE 4: GESTIONE SUCCESSO
                // Mostra messaggio di successo
                showAlert('success', `Benvenuto ${authResponse.user.username}!`);

                // Attende 1.5 secondi per mostrare messaggio
                await new Promise(resolve => setTimeout(resolve, 1500));

                // FASE 5: REINDIRIZZAMENTO
                // TODO: Sostituire con URL dashboard quando sarà creata
                console.log('Token salvati:', {
                    access_token: TokenService.getAccessToken()?.substring(0, 20) + '...',
                    refresh_token: TokenService.getRefreshToken()?.substring(0, 20) + '...',
                    expires_in: TokenService.getSecondsUntilExpiry() + ' secondi'
                });
                
                // Per ora rimane sulla pagina login
                // window.location.href = '/dashboard';

            } catch (error) {
                // FASE 4 (ALTERNATIVA): GESTIONE ERRORI
                console.error('Errore login:', error);
                
                // Mostra messaggio di errore
                let errorMessage = 'Errore durante il login. Riprova.';
                
                if (error.message) {
                    errorMessage = error.message;
                }
                
                showAlert('danger', errorMessage);
                
                // Aggiunge classe di errore ai campi
                document.getElementById('email').classList.add('is-invalid');
                document.getElementById('password').classList.add('is-invalid');

            } finally {
                // FASE 5: RIPRISTINO UI
                // Riabilita pulsante e ripristina testo originale
                submitButton.disabled = false;
                submitButton.innerHTML = submitButtonText;
            }
        });

        /* ============================================
           FUNZIONE: MOSTRA ALERT
           ============================================
           Crea e mostra un alert Bootstrap nella pagina
           
           @param {string} type - Tipo alert (success, danger, warning, info)
           @param {string} message - Messaggio da mostrare
        */
        function showAlert(type, message) {
            // Rimuove alert esistenti
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            // Crea nuovo alert
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

            // Aggiunge al body
            document.body.appendChild(alertDiv);

            // Rimuove automaticamente dopo 5 secondi
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }

        /* ============================================
           FUNZIONE: RIMUOVI ERRORI CAMPI
           ============================================
           Rimuove le classi di errore dai campi input
        */
        function clearFieldErrors() {
            document.getElementById('email').classList.remove('is-invalid');
            document.getElementById('password').classList.remove('is-invalid');
        }
    </script>
</body>
</html>
