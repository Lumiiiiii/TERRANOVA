# 🌿 Gestionale TerraNova - Naturopatia

Sistema gestionale web per la gestione di pazienti, visite e anamnesi, ottimizzato per naturopati.
**Versione 2.0 - Refactoring Completo**

## 📋 Nuove Caratteristiche

- ✅ **Interfaccia Moderna**: Design completamente rinnovato con **Tailwind CSS**.
- ✅ **Database Ottimizzato**: Struttura semplificata e pulita (`terranova_naturopata`).
- ✅ **Gestione Pazienti**:
  - CRUD completo (Creazione, Modifica, Ricerca, Eliminazione).
  - Rimozione campi obsoleti (es. Sesso).
  - Calcolo automatico età.
- ✅ **Visite e Anamnesi**:
  - **Anamnesi Generale**: Storia clinica fissa (Allergie, Patologie, ecc.).
  - **Visita Singola**: Dettagli specifici per ogni seduta (Umore, Sintomi, Digestione, ecc.).
- ✅ **Piani Terapeutici**:
  - Gestione **Medicinali** (Omeopatici, Fitoterapici, Integratori).
  - Gestione **Prescrizioni** (Attive, Storico, Dosaggi).
  - Gestione **Alimenti da Evitare** (per categoria).

## 🛠️ Tecnologie Utilizzate

- **Backend**: PHP 7.4+ (OOP, PDO, Singleton Pattern)
- **Database**: MySQL / MariaDB (Schema relazionale normalizzato)
- **Frontend**: HTML5, **Tailwind CSS** (via CDN), JavaScript (Fetch API)
- **Server**: Apache (XAMPP/WAMP/MAMP)

## 📦 Installazione Aggiornata

1. **Configurazione Database**
   - Importa il file `migration.sql` nel tuo database manager (phpMyAdmin).
   - Questo creerà il database `terranova_naturopata`.

2. **Configurazione Connessione**
   - Il file `config/database.php` è già configurato:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'terranova_naturopata'); // Nuovo DB
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Avvio**
   - Assicurati che Apache e MySQL siano attivi.
   - Visita `http://localhost/TERRANOVA/index.php`.

## 📂 Struttura del Progetto (Aggiornata)

```
TERRANOVA/
├── config/
│   └── database.php          # Connessione DB
├── includes/
│   ├── Patient.php           # Logica Pazienti
│   ├── Visit.php             # Logica Visite
│   ├── Anamnesis.php         # Logica Anamnesi Generale
│   ├── Medicine.php          # Logica Medicinali
│   ├── Prescription.php      # Logica Prescrizioni
│   └── FoodRestrictions.php  # Logica Alimenti
├── css/
│   └── style.css             # Custom styles
├── js/
│   └── main.js               # Frontend Logic
├── ajax_handlers.php         # API Endpoint per chiamate AJAX
├── index.php                 # Dashboard Dashboard
├── paziente_nuovo.php        # Form Creazione
├── paziente_dettaglio.php    # Scheda Paziente
├── visita_anamnesi.php       # Form Visita & Anamnesi
├── medicinali_gestione.php   # Catalogo Medicinali
├── prescrizioni_gestione.php # Piano Terapeutico
├── alimenti_gestione.php     # Alimenti da Evitare
├── migration.sql             # Schema Database Corrente
└── README.md                 # Documentazione
```

## 🔐 Note di Sviluppo

- Il codice è stato ripulito da logiche obsolete.
- Tutte le classi backend sono state riscritte per corrispondere esattamente al nuovo schema DB.
- L'interfaccia frontend è stata unificata con uno stile coerente (Glassmorphism + Tailwind).

---

**Sviluppato per**: Progetto Scolastico Gestionale Naturologa
**Ultimo Aggiornamento**: Febbraio 2026
