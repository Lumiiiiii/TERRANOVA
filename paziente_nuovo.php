<?php
/**
 * Form per aggiungere un nuovo paziente - Refactored with Tailwind CSS
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
                extend: {
                    colors: {
                        primary: '#2d8659',
                        secondary: '#f4a261',
                        accent: '#e76f51',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 glass px-6 py-4 mb-8">
        <div class="max-w-2xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-green-400 flex items-center justify-center text-white font-bold">TN</a>
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500 font-medium">Gestionale Naturopatia</span>
                    <h1 class="text-sm font-bold text-gray-800 leading-none">Nuovo Paziente</h1>
                </div>
            </div>
            <div>
                 <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Torna alla Home
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 pb-20">
        
        <div class="mb-8 animate-fade-in">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Registrazione Paziente</h1>
            <p class="text-gray-500">Compila la scheda per inserire un nuovo paziente nel database.</p>
        </div>

        <div class="glass-card animate-fade-in delay-100">
            <form id="patient-form" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-sm font-medium text-gray-700">Nome e Cognome *</label>
                        <input 
                            type="text" 
                            id="nome_cognome" 
                            name="nome_cognome" 
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 text-base" 
                            required
                            placeholder="Es: Mario Rossi"
                        >
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Sesso</label>
                        <select id="sesso" name="sesso" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 bg-white">
                            <option value="">Seleziona...</option>
                            <option value="F">Femmina</option>
                            <option value="M">Maschio</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Data di Nascita</label>
                        <input 
                            type="date" 
                            id="data_nascita" 
                            name="data_nascita" 
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Telefono</label>
                        <input 
                            type="tel" 
                            id="telefono" 
                            name="telefono" 
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50"
                            placeholder="Es: 333 1234567"
                        >
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50"
                        placeholder="Es: mario.rossi@email.com"
                    >
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Indirizzo</label>
                    <textarea 
                        id="indirizzo" 
                        name="indirizzo" 
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50"
                        rows="2"
                        placeholder="Es: Via Roma 123, 00100 Roma"
                    ></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Professione / Note</label>
                    <textarea 
                        id="professione" 
                        name="professione" 
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50"
                        rows="2"
                        placeholder="Professione o altre note rapide"
                    ></textarea>
                </div>
                
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-4">
                    <a href="index.php" class="px-6 py-2 rounded-lg text-gray-600 font-medium hover:bg-gray-100 transition-colors">Annulla</a>
                    <button type="submit" class="px-8 py-2 rounded-lg bg-primary text-white font-bold shadow-lg shadow-green-500/30 hover:shadow-green-500/50 hover:bg-green-700 transition-all transform hover:-translate-y-0.5">
                        Crea Paziente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.getElementById('patient-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const success = await createPatient(formData);
            if (success) {
                // Redirect gesture handled by createPatient or do it here
                // main.js usually handles the redirect on success or returns true
            }
        });
    </script>
</body>
</html>
