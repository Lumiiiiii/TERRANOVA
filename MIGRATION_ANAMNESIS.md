# MIGRAZIONE ANAMNESI VERSO SCHEMA SEMPLIFICATO

Per aggiornare il database alla nuova versione (Anamnesi Generale separata + Questionario Visita integrato nella tabella `visite`), eseguire i seguenti passaggi:

1.  Assicurarsi che il database `naturologa_db` sia attivo.
2.  Eseguire lo script SQL `simplify_anamnesi_db.sql` incluso in questo commit.

Questo script eseguirà le seguenti operazioni:
-   Aggiungerà le colonne del questionario (sonno, stress, digestione, ecc.) alla tabella `visite`.
-   Collegherà la tabella `anamnesi` direttamente alla tabella `pazienti` (per la storia clinica generale).
-   Migrerà i dati esistenti (se possibile) verso la nuova struttura.

## File Modificati/Aggiunti:
-   `simplify_anamnesi_db.sql`: Script di migrazione database.
-   `visita_anamnesi.php`: Interfaccia aggiornata per la nuova gestione.
-   `includes/Anamnesis.php`: Classe aggiornata per gestire solo la storia clinica.
-   `includes/Visit.php`: Classe aggiornata per gestire anche i dati del questionario.
-   `ajax_handlers.php`: API aggiornate per supportare il nuovo flusso.
