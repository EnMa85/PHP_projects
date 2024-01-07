CREATE DATABASE registro;
USE registro;

CREATE TABLE `docenti` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `corsisti` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `cf` varchar(16) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `utenti` (
  `id_docente` int PRIMARY KEY,
  `email` varchar(30) NOT NULL UNIQUE,
  `password` varchar(100) NOT NULL,
  `account_attivo` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (id_docente) REFERENCES docenti(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `categorie` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `nome` varchar(50) NOT NULL UNIQUE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `corsi` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `titolo` varchar(200) NOT NULL,
  `id_categoria` int NOT NULL,
  FOREIGN KEY (id_categoria) REFERENCES categorie(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `giorni_festivi` (
   `id` int AUTO_INCREMENT PRIMARY KEY,
   `data_festiva` date NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `info_corsi` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `id_titolo` int NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL,
  `totale_ore` int(5),
  FOREIGN KEY (id_titolo) REFERENCES corsi(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `lezioni` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `id_corso` int NOT NULL,
  `giorno` date NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL,
  FOREIGN KEY (id_corso) REFERENCES info_corsi(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `corsi_per_corsista` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `id_corso` int NOT NULL,
  `id_corsista` int NOT NULL,
  FOREIGN KEY (id_corso) REFERENCES info_corsi(`id`) ON UPDATE CASCADE,
  FOREIGN KEY (id_corsista) REFERENCES corsisti(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `corsi_per_docente` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `id_corso` int NOT NULL,
  `id_docente` int NOT NULL,
  FOREIGN KEY (id_corso) REFERENCES info_corsi(`id`) ON UPDATE CASCADE,
  FOREIGN KEY (id_docente) REFERENCES docenti(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `lezioni_per_docente` (
  `id_lezione` int PRIMARY KEY,
  `id_docente` int NOT NULL,
  FOREIGN KEY (id_lezione) REFERENCES lezioni(`id`) ON UPDATE CASCADE,
  FOREIGN KEY (id_docente) REFERENCES docenti(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `registro_assenze` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `id_lezione` int NOT NULL,
  `id_corsista` int NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL,
  `note` varchar(200) NOT NULL,
  FOREIGN KEY (id_lezione) REFERENCES lezioni(`id`) ON UPDATE CASCADE,
  FOREIGN KEY (id_corsista) REFERENCES corsisti(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `stato_presenze` (
  `id` int AUTO_INCREMENT PRIMARY KEY,
  `id_lezione` int NOT NULL,
  `id_corsista` int NOT NULL,
  `stato` enum('assente', 'presente'),
  `orario_varazione` time NOT NULL,
  FOREIGN KEY (id_corsista) REFERENCES corsisti(`id`) ON UPDATE CASCADE,
  FOREIGN KEY (id_lezione) REFERENCES lezioni(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;