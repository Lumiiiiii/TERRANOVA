

DROP DATABASE IF EXISTS terranova_naturopata;
CREATE DATABASE IF NOT EXISTS terranova_naturopata;
USE terranova_naturopata;


CREATE TABLE pazienti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_cognome VARCHAR(255) NOT NULL,
    data_nascita DATE,
    telefono VARCHAR(20),
    indirizzo VARCHAR(255),
    email VARCHAR(100),
    professione VARCHAR(100),
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE anamnesi (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT UNIQUE,  
    allergie_intolleranze TEXT, 
    patologie_pregresse TEXT, 
    interventi_chirurgici TEXT, 
    esami_clinici_recenti TEXT, 
    terapie_farmacologiche_croniche TEXT, 
    alcol VARCHAR(100), 
    fumo VARCHAR(100), 
    traumi_o_fratture TEXT, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id) ON DELETE CASCADE
);

CREATE TABLE visite (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT, 
    data_visita DATE, 
    motivazione TEXT, 
    concentrazione VARCHAR(100), 
    stato_emotivo TEXT, 
    attivita_fisica TEXT, 
    idratazione TEXT, 
    qualita_sonno_percepita TEXT, 
    ore_sonno DECIMAL(4,2), 
    sintomi_acuti TEXT, 
    regolarita_intestinale TEXT, 
    appetito_e_digestione TEXT, 
    difficolta_addormentarsi_risvegli_notturni TEXT, 
    livello_stress INT, 
    livello_energia INT, 
    supporti_in_uso TEXT, 
    alimentazione_recente TEXT, 
    note_finali TEXT, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id) ON DELETE CASCADE
);


CREATE TABLE medicinali (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nome VARCHAR(255) NOT NULL, 
    tipologia VARCHAR(100), 
    formato VARCHAR(100), 
    dosaggio_standard VARCHAR(100), 
    attivo BOOLEAN DEFAULT TRUE, 
    note TEXT, 
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
);


CREATE TABLE prescrizioni (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT, 
    medicinale_id INT, 
    visita_id INT, 
    dosaggio VARCHAR(100), 
    frequenza VARCHAR(100), 
    durata VARCHAR(100), 
    note_prescrizione TEXT, 
    data_inizio DATE, 
    data_fine DATE, 
    attivo BOOLEAN DEFAULT TRUE, 
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id),
    FOREIGN KEY (medicinale_id) REFERENCES medicinali(id),
    FOREIGN KEY (visita_id) REFERENCES visite(id)
);


CREATE TABLE lista_alimenti (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nome VARCHAR(100), 
    ordine INT 
);


CREATE TABLE alimenti_evitare (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT, 
    lista_alimenti_id INT, 
    attivo BOOLEAN DEFAULT TRUE, 
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id),
    FOREIGN KEY (lista_alimenti_id) REFERENCES lista_alimenti(id)
);



DROP DATABASE IF EXISTS terranova_naturopata;
CREATE DATABASE IF NOT EXISTS terranova_naturopata;
USE terranova_naturopata;


CREATE TABLE pazienti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_cognome VARCHAR(255) NOT NULL,
    data_nascita DATE,
    telefono VARCHAR(20),
    indirizzo VARCHAR(255),
    email VARCHAR(100),
    professione VARCHAR(100),
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE anamnesi (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT UNIQUE, 
    allergie_intolleranze TEXT, 
    patologie_pregresse TEXT, 
    interventi_chirurgici TEXT, 
    esami_clinici_recenti TEXT, 
    terapie_farmacologiche_croniche TEXT, 
    alcol VARCHAR(100), 
    fumo VARCHAR(100), 
    traumi_o_fratture TEXT, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id) ON DELETE CASCADE
);

CREATE TABLE visite (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT, 
    data_visita DATE, 
    motivazione TEXT, 
    concentrazione VARCHAR(100), 
    stato_emotivo TEXT, 
    attivita_fisica TEXT, 
    idratazione TEXT, 
    qualita_sonno_percepita TEXT, 
    ore_sonno DECIMAL(4,2), 
    sintomi_acuti TEXT, 
    regolarita_intestinale TEXT, 
    appetito_e_digestione TEXT, 
    difficolta_addormentarsi_risvegli_notturni TEXT, 
    livello_stress INT, 
    livello_energia INT, 
    supporti_in_uso TEXT, 
    alimentazione_recente TEXT, 
    note_finali TEXT, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id) ON DELETE CASCADE
);

CREATE TABLE medicinali (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nome VARCHAR(255) NOT NULL, 
    tipologia VARCHAR(100), 
    formato VARCHAR(100), 
    dosaggio_standard VARCHAR(100), 
    attivo BOOLEAN DEFAULT TRUE, 
    note TEXT, 
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
);

CREATE TABLE prescrizioni (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT, 
    medicinale_id INT, 
    visita_id INT, 
    dosaggio VARCHAR(100), 
    frequenza VARCHAR(100), 
    durata VARCHAR(100), 
    note_prescrizione TEXT, 
    data_inizio DATE, 
    data_fine DATE, 
    attivo BOOLEAN DEFAULT TRUE, 
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id),
    FOREIGN KEY (medicinale_id) REFERENCES medicinali(id),
    FOREIGN KEY (visita_id) REFERENCES visite(id)
);

CREATE TABLE lista_alimenti (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nome VARCHAR(100), 
    ordine INT 
);

CREATE TABLE alimenti_evitare (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    paziente_id INT, 
    lista_alimenti_id INT, 
    attivo BOOLEAN DEFAULT TRUE, 
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    FOREIGN KEY (paziente_id) REFERENCES pazienti(id),
    FOREIGN KEY (lista_alimenti_id) REFERENCES lista_alimenti(id)
);
