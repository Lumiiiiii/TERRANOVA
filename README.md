# 🌿 Gestionale Naturologa

Sistema gestionale web per la gestione di pazienti, visite e anamnesi per naturologi.

## 📋 Caratteristiche

- ✅ Gestione completa pazienti (CRUD)
- ✅ Ricerca pazienti per nome, telefono o email
- ✅ Creazione e gestione visite
- ✅ Compilazione schede anambestiche dettagliate
- ✅ Storico completo delle visite per ogni paziente
- ✅ Gestione alimenti da evitare per paziente
- ✅ Interfaccia moderna e responsive
- ✅ Database MySQL con struttura relazionale

## 🛠️ Tecnologie Utilizzate

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Server**: Apache/Nginx con supporto PHP

## 📦 Installazione

### Requisiti

- XAMPP, WAMP, MAMP o simili (con PHP e MySQL)
- Browser moderno (Chrome, Firefox, Edge, Safari)

### Passi di Installazione

1. **Copia i file del progetto**
   - Copia la cartella `TERRANOVA` nella directory del tuo server web
   - Se usi XAMPP, copia in `C:\xampp\htdocs\`
   - Se usi WAMP, copia in `C:\wampXX\www\`

2. **Crea il database**
   - Apri phpMyAdmin (di solito su `http://localhost/phpmyadmin`)
   - Clicca su "Nuovo" o "New" per creare un database
   - Importa il file `database_schema.sql`:
     1. Clicca sul database appena creato
     2. Vai sulla tab "Importa" o "Import"
     3. Seleziona il file `database_schema.sql`
     4. Clicca "Esegui" o "Execute"

   **Oppure** esegui manualmente:
   ```bash
   mysql -u root -p < database_schema.sql
   ```

3. **Configura la connessione al database**
   - Apri il file `config/database.php`
   - Modifica i parametri di connessione se necessario:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'naturologa_db');
   define('DB_USER', 'root');        // Il tuo username MySQL
   define('DB_PASS', '');            // La tua password MySQL
   ```

4. **Avvia il server**
   - Avvia Apache e MySQL dal pannello di controllo XAMPP/WAMP
   - Apri il browser e vai su `http://localhost/TERRANOVA/index.php`

## 📖 Guida all'Uso

### 1. Homepage
- Visualizza pazienti recenti
- Ricerca pazienti per nome, telefono o email
- Pulsante rapido per aggiungere nuovo paziente

### 2. Aggiungere un Paziente
- Clicca su "Nuovo Paziente"
- Compila i dati anagrafici (nome e cognome obbligatori)
- Salva per creare il paziente

### 3. Gestione Paziente
- Nella scheda dettaglio paziente puoi:
  - Modificare i dati anagrafici
  - Visualizzare lo storico delle visite
  - Gestire gli alimenti da evitare
  - Creare nuove visite

### 4. Compilazione Anamnesi
- Dalla scheda paziente, clicca "Nuova Visita"
- Compila la scheda anamnestica con tutte le sezioni:
  - Anamnesi personale
  - Qualità del sonno
  - Livello di stress (scala 1-10)
  - Stato psico-fisico
  - Attività fisica
  - Alimentazione
  - Supporti utilizzati (farmaci, integratori)
  - Osservazioni finali

### 5. Storico Visite
- Visualizza tutte le visite precedenti
- Consulta le anamnesi compilate
- Modifica anamnesi esistenti

### 6. Alimenti da Evitare
- Aggiungi alimenti da evitare organizzati per categoria
- Le categorie predefinite includono le sostanze dalla lista fornita
- Rimuovi alimenti quando non più necessari

## 📂 Struttura del Progetto

```
TERRANOVA/
├── config/
│   └── database.php          # Configurazione database
├── includes/
│   ├── Patient.php           # Gestione pazienti
│   ├── Visit.php             # Gestione visite
│   ├── Anamnesis.php         # Gestione anamnesi
│   └── FoodRestrictions.php  # Gestione alimenti
├── css/
│   └── style.css             # Stili CSS
├── js/
│   └── main.js               # JavaScript utilities
├── index.php                 # Homepage
├── paziente_nuovo.php        # Form nuovo paziente
├── paziente_dettaglio.php    # Dettaglio paziente
├── visita_anamnesi.php       # Form anamnesi
├── visita_storico.php        # Visualizzazione visita
├── alimenti_gestione.php     # Gestione alimenti
├── ajax_handlers.php         # Handler API AJAX
├── database_schema.sql       # Schema del database
└── README.md                 # Questo file
```

## 🗄️ Database

### Tabelle Principali

- **pazienti**: Dati anagrafici pazienti
- **visite**: Record delle visite effettuate
- **anamnesi**: Schede anambestiche complete
- **alimenti_evitare**: Alimenti da evitare per paziente
- **categorie_alimenti**: Categorie predefinite di alimenti

## 🔒 Sicurezza

- Prepared statements PDO per prevenire SQL injection
- Validazione input lato client e server
- Encoding UTF-8 per caratteri speciali
- Sanitizzazione output HTML con `htmlspecialchars()`

## 🐛 Risoluzione Problemi

### Errore di connessione al database
- Verifica che MySQL sia avviato
- Controlla username e password in `config/database.php`
- Verifica che il database `naturologa_db` esista

### Pagina bianca o errori PHP
- Abilita la visualizzazione errori in `php.ini`:
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
- Controlla i log di errore in `C:\xampp\apache\logs\error.log`

### CSS/JS non caricati
- Verifica che i percorsi siano corretti
- Pulisci la cache del browser (Ctrl+F5)
- Controlla che i file esistano nelle cartelle `css/` e `js/`

## 📝 Prossimi Sviluppi (Opzionali)

- [ ] Sistema di login e autenticazione
- [ ] Esportazione PDF delle schede
- [ ] Gestione appuntamenti con calendario
- [ ] Statistiche e grafici
- [ ] Backup automatico database
- [ ] Sistema di notifiche

## 👨‍💻 Supporto

Per assistenza o segnalazione bug, contatta lo sviluppatore del progetto.

## 📄 Licenza

Progetto scolastico - Libero utilizzo per scopi educativi.

---

**Versione**: 1.0.0  
**Data**: Febbraio 2026  
**Sviluppato per**: Progetto Scolastico Gestionale Naturologa
