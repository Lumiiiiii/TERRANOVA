<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: { colors: { primary: '#2d8659', secondary: '#f4a261', accent: '#e76f51' } }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen text-gray-800">
    <nav class="sticky top-0 z-50 glass px-6 py-4 mb-8">
        <div class="max-w-2xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php"
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <h1 class="text-sm font-bold text-gray-800">Nuovo Paziente</h1>
            </div>
            <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-primary">Torna alla Home</a>
        </div>
    </nav>
    <div class="max-w-2xl mx-auto px-4 pb-20">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Registrazione Paziente</h1>
        <div class="glass-card animate-fade-in">
            <form id="patient-form" class="space-y-6">
                <!-- Action for AJAX -->
                <input type="hidden" name="action" value="create_patient">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-sm font-medium text-gray-700">Nome e Cognome *</label>
                        <input type="text" name="nome_cognome"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200" required
                            placeholder="Es: Mario Rossi">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Data di Nascita</label>
                        <input type="date" name="data_nascita"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Telefono</label>
                        <input type="tel" name="telefono" class="w-full px-4 py-2 rounded-lg border border-gray-200"
                            placeholder="Es: 333 1234567">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2 rounded-lg border border-gray-200"
                        placeholder="Es: mario.rossi@email.com">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Indirizzo</label>
                    <textarea name="indirizzo" class="w-full px-4 py-2 rounded-lg border border-gray-200" rows="2"
                        placeholder="Es: Via Roma 123"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Professione</label>
                    <input type="text" name="professione" class="w-full px-4 py-2 rounded-lg border border-gray-200"
                        placeholder="Es: Impiegato">
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-4">
                    <a href="index.php"
                        class="px-6 py-2 rounded-lg text-gray-600 font-medium hover:bg-gray-100">Annulla</a>
                    <button type="submit"
                        class="px-8 py-2 rounded-lg bg-primary text-white font-bold hover:bg-green-700 transition-colors">Crea
                        Paziente</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/main.js"></script>
    <script>
        document.getElementById('patient-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            // Append explicit action if not in form
            // formData.append('action', 'create_patient'); 

            try {
                const response = await fetch('ajax_handlers.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    window.location.href = 'paziente_dettaglio.php?id=' + result.id;
                } else {
                    alert('Errore: ' + (result.error || 'Sconosciuto'));
                }
            } catch (e) { console.error(e); alert('Errore di connessione'); }
        });
    </script>
</body>

</html>