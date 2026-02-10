<?php
/**
 * Form per aggiungere un nuovo paziente
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Paziente - TerraNova</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>TerraNova - Gestionale Naturopatia</h1>
            <nav class="header-nav">
                <a href="index.php">Home</a>
                <a href="paziente_nuovo.php">Nuovo Paziente</a>
                <a href="medicinali_gestione.php">Medicinali</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <h2 class="card-header">Nuovo Paziente</h2>
            
            <form id="patient-form" method="POST">
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label" for="nome_cognome">Nome e Cognome *</label>
                        <input 
                            type="text" 
                            id="nome_cognome" 
                            name="nome_cognome" 
                            class="form-input" 
                            required
                            placeholder="Es: Mario Rossi"
                        >
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label" for="sesso">Sesso</label>
                        <select id="sesso" name="sesso" class="form-input">
                            <option value="">Seleziona...</option>
                            <option value="F">Femmina</option>
                            <option value="M">Maschio</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="data_nascita">Data di Nascita</label>
                        <input 
                            type="date" 
                            id="data_nascita" 
                            name="data_nascita" 
                            class="form-input"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="telefono">Telefono</label>
                        <input 
                            type="tel" 
                            id="telefono" 
                            name="telefono" 
                            class="form-input"
                            placeholder="Es: 333 1234567"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input"
                        placeholder="Es: mario.rossi@email.com"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="indirizzo">Indirizzo</label>
                    <textarea 
                        id="indirizzo" 
                        name="indirizzo" 
                        class="form-textarea"
                        rows="2"
                        placeholder="Es: Via Roma 123, 00100 Roma"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="professione">Professione</label>
                    <input 
                        type="text" 
                        id="professione" 
                        name="professione" 
                        class="form-input"
                        placeholder="Es: Insegnante"
                    >
                </div>

                <div class="form-row" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Salva Paziente</button>
                    <a href="index.php" class="btn btn-outline">↶ Annulla</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.getElementById('patient-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const success = await savePatient(formData, true);
            
            if (success) {
                this.reset();
            }
        });
    </script>
</body>
</html>
