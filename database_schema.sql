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
-- Indici aggiuntivi per performance
-- ============================================
CREATE INDEX idx_visita_paziente_data ON visite(paziente_id, data_visita DESC);
CREATE INDEX idx_alimenti_paziente_attivo ON alimenti_evitare(paziente_id, attivo);
