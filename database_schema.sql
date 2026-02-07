-- ============================================
-- Database Schema per Gestionale Naturologa
-- ============================================

CREATE DATABASE IF NOT EXISTS naturologa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE naturologa_db;

-- ============================================
-- Tabella PAZIENTI
-- ============================================
CREATE TABLE IF NOT EXISTS pazienti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_cognome VARCHAR(255) NOT NULL,
    data_nascita DATE,
    indirizzo TEXT,
    telefono VARCHAR(50),
    email VARCHAR(255),
    professione VARCHAR(255),
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome_cognome),
    INDEX idx_data_nascita (data_nascita)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabella VISITE
-- ============================================
CREATE TABLE IF NOT EXISTS visite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paziente_id INT NOT NULL,
    data_visita DATE NOT NULL,
    note_finali TEXT,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id) ON DELETE CASCADE,
    INDEX idx_paziente (paziente_id),
    INDEX idx_data_visita (data_visita)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabella ANAMNESI
-- ============================================
CREATE TABLE IF NOT EXISTS anamnesi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visita_id INT NOT NULL UNIQUE,
    
    -- Anamnesi Personale
    vomito ENUM('Si', 'No', 'Dettagli') DEFAULT 'No',
    vomito_dettagli TEXT,
    febbre ENUM('Si', 'No', 'Dettagli') DEFAULT 'No',
    febbre_dettagli TEXT,
    flusso ENUM('Si', 'No', 'Dettagli') DEFAULT 'No',
    flusso_dettagli TEXT,
    alcol ENUM('Si', 'No', 'Dettagli') DEFAULT 'No',
    alcol_dettagli TEXT,
    patologie TEXT,
    interventi_chirurgici TEXT,
    fratture_traumi TEXT,
    
    -- Qualità del Sonno
    qualita_sonno VARCHAR(100),
    ore_sonno DECIMAL(3,1),
    risvegli_notturni BOOLEAN DEFAULT FALSE,
    difficolta_addormentarsi BOOLEAN DEFAULT FALSE,
    qualita_risveglio VARCHAR(100),
    
    -- Livello di Stress (1-10)
    livello_stress INT,
    
    -- Stato Psico-Fisico
    appetito VARCHAR(100),
    ansia VARCHAR(100),
    umore VARCHAR(100),
    motivazione VARCHAR(100),
    concentrazione VARCHAR(100),
    
    -- Stile di Vita - Attività Fisica
    attivita_fisica_frequenza VARCHAR(100),
    attivita_fisica_tipo VARCHAR(255),
    
    -- Stile di Vita - Alimentazione
    alimentazione_generale TEXT,
    
    -- Supporti Utilizzati
    farmaci_categoria VARCHAR(255),
    farmaci_specifiche TEXT,
    integratori_categoria VARCHAR(255),
    integratori_specifiche TEXT,
    rimedi_naturali TEXT,
    terapie_corso TEXT,
    
    -- Osservazioni Finali
    osservazioni_finali TEXT,
    
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (visita_id) REFERENCES visite(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabella ALIMENTI DA EVITARE
-- ============================================
CREATE TABLE IF NOT EXISTS alimenti_evitare (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paziente_id INT NOT NULL,
    categoria VARCHAR(255) NOT NULL,
    alimento VARCHAR(255) NOT NULL,
    attivo BOOLEAN DEFAULT TRUE,
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id) ON DELETE CASCADE,
    INDEX idx_paziente_categoria (paziente_id, categoria),
    INDEX idx_attivo (attivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabella CATEGORIE ALIMENTI (lista predefinita)
-- ============================================
CREATE TABLE IF NOT EXISTS categorie_alimenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    ordine INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserimento categorie dalla lista fornita
INSERT INTO categorie_alimenti (nome, ordine) VALUES
('Causticum D30', 1),
('Ca-Mg-Fosfato', 2),
('Fenolo', 3),
('Anatra', 4),
('Agnello', 5),
('Gallina', 6),
('Vitello', 7),
('Tacchino', 8),
('Manzo', 9),
('Carne Maiale', 10),
('Grassi Maiale', 11),
('Albume uovo', 12),
('Tuorlo uovo', 13),
('Latte di mucca', 14),
('Siero Latte', 15),
('Formaggio', 16),
('Yogurt', 17),
('Ricotta', 18),
('Latte Cagliato', 19),
('Margarina', 20);

-- ============================================
-- Tabella MEDICINALI (Catalogo prodotti prescrivibili)
-- ============================================
CREATE TABLE IF NOT EXISTS medicinali (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipologia ENUM('Omeopatico', 'Fitoterapico', 'Integratore', 'Altro') DEFAULT 'Altro',
    formato VARCHAR(100),
    dosaggio_standard VARCHAR(255),
    note TEXT,
    attivo BOOLEAN DEFAULT TRUE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nome (nome),
    INDEX idx_tipologia (tipologia),
    INDEX idx_attivo (attivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserimento medicinali di esempio
INSERT INTO medicinali (nome, tipologia, formato, dosaggio_standard, note) VALUES
('Arnica Montana 9CH', 'Omeopatico', 'Granuli', '5 granuli 3 volte al giorno', 'Per traumi e dolori muscolari'),
('Nux Vomica 9CH', 'Omeopatico', 'Granuli', '5 granuli 2-3 volte al giorno', 'Per disturbi digestivi e stress'),
('Belladonna 9CH', 'Omeopatico', 'Granuli', '5 granuli ogni 2 ore', 'Per stati febbrili acuti'),
('Chamomilla 9CH', 'Omeopatico', 'Granuli', '5 granuli 3 volte al giorno', 'Per nervosismo e dolori'),
('Calendula Officinalis TM', 'Fitoterapico', 'Tintura Madre', '30-40 gocce 2 volte al giorno', 'Cicatrizzante e antinfiammatorio'),
('Passiflora TM', 'Fitoterapico', 'Tintura Madre', '30 gocce prima di dormire', 'Per ansia e insonnia'),
('Echinacea TM', 'Fitoterapico', 'Tintura Madre', '20 gocce 3 volte al giorno', 'Immunostimolante'),
('Valeriana TM', 'Fitoterapico', 'Tintura Madre', '40 gocce la sera', 'Per sonno e rilassamento'),
('Magnesio Supremo', 'Integratore', 'Polvere', '1 cucchiaino al giorno', 'Supporto muscolare e nervoso'),
('Vitamina D3', 'Integratore', 'Gocce', '4 gocce al giorno', 'Supporto sistema immunitario'),
('Omega 3', 'Integratore', 'Capsule', '1-2 capsule al giorno', 'Antiinfiammatorio naturale'),
('Probiotici', 'Integratore', 'Capsule', '1 capsula al mattino', 'Equilibrio flora intestinale');

-- ============================================
-- Tabella PRESCRIZIONI (Medicinali prescritti ai pazienti)
-- ============================================
CREATE TABLE IF NOT EXISTS prescrizioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paziente_id INT NOT NULL,
    visita_id INT,
    medicinale_id INT NOT NULL,
    dosaggio VARCHAR(255),
    frequenza VARCHAR(255),
    durata VARCHAR(100),
    note_prescrizione TEXT,
    data_inizio DATE,
    data_fine DATE,
    attivo BOOLEAN DEFAULT TRUE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id) ON DELETE CASCADE,
    FOREIGN KEY (visita_id) REFERENCES visite(id) ON DELETE SET NULL,
    FOREIGN KEY (medicinale_id) REFERENCES medicinali(id) ON DELETE RESTRICT,
    
    INDEX idx_paziente (paziente_id),
    INDEX idx_visita (visita_id),
    INDEX idx_medicinale (medicinale_id),
    INDEX idx_attivo (attivo),
    INDEX idx_paziente_attivo (paziente_id, attivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Vista per elenco completo visite con paziente
-- ============================================
CREATE OR REPLACE VIEW vista_visite_complete AS
SELECT 
    v.id AS visita_id,
    v.data_visita,
    v.note_finali,
    v.data_creazione AS visita_creazione,
    p.id AS paziente_id,
    p.nome_cognome,
    p.data_nascita,
    p.telefono,
    p.email,
    YEAR(CURDATE()) - YEAR(p.data_nascita) AS eta,
    CASE WHEN a.id IS NOT NULL THEN TRUE ELSE FALSE END AS ha_anamnesi
FROM visite v
JOIN pazienti p ON v.paziente_id = p.id
LEFT JOIN anamnesi a ON v.id = a.visita_id
ORDER BY v.data_visita DESC;

-- ============================================
-- Vista per prescrizioni complete (con dettagli medicinale e paziente)
-- ============================================
CREATE OR REPLACE VIEW vista_prescrizioni_complete AS
SELECT 
    pr.id AS prescrizione_id,
    pr.paziente_id,
    p.nome_cognome AS paziente_nome,
    pr.visita_id,
    v.data_visita,
    pr.medicinale_id,
    m.nome AS medicinale_nome,
    m.tipologia AS medicinale_tipologia,
    m.formato AS medicinale_formato,
    pr.dosaggio,
    pr.frequenza,
    pr.durata,
    pr.note_prescrizione,
    pr.data_inizio,
    pr.data_fine,
    pr.attivo,
    pr.data_creazione AS prescrizione_creazione
FROM prescrizioni pr
JOIN pazienti p ON pr.paziente_id = p.id
JOIN medicinali m ON pr.medicinale_id = m.id
LEFT JOIN visite v ON pr.visita_id = v.id
ORDER BY pr.data_inizio DESC, pr.attivo DESC;

-- ============================================
-- Indici aggiuntivi per performance
-- ============================================
CREATE INDEX idx_visita_paziente_data ON visite(paziente_id, data_visita DESC);
CREATE INDEX idx_alimenti_paziente_attivo ON alimenti_evitare(paziente_id, attivo);

