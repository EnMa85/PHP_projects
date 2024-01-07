USE registro;

INSERT INTO docenti (nome, cognome) 
VALUES 
    ('Tizio'     ,  'Bianchi'),
    ('Caio'      ,     'Neri'),
    ('Sempronio' ,    'Rossi'),
    ('Pinco'     ,  'Pallino'),
    ('Pino'.     ,  'Calzino'),
    ('Mickey'    ,    'Mouse'),
    ('Donald'    ,     'Duck'),
    ('Daisy'     ,     'Duck'),
    ('Pippo'     ,     'Cane'),
    ('Silvestro' ,    'Gatto');


INSERT INTO corsisti (nome, cognome, cf) 
VALUES 
    ('Ada'      ,   'Adi'      , 'AAAAAA00A00A000A'),
    ('Bruno'    ,   'Bruni'    , 'BBBBBB00B00B000B'),
    ('Carlo'    ,  'Carlino'   , 'CCCCCC00C00C000C'),
    ('Dino'     ,  'Doni'      , 'DDDDDD00D00D000D'),
    ('Ezio'     ,  'Ozi'       , 'EEEEEE00E00E000E'),
    ('Franco'   ,  'Franchi'   , 'FFFFFF00F00F000F'),
    ('Gino'     ,  'Gini'      , 'GGGGGG00G00G000G'),
    ('Hugh'     ,  'Hugo'      , 'HHHHHH00H00H000H'),
    ('Ignazio'  ,  'Ignis'     , 'IIIIII00I00I000I'),
    ('Johnny'   ,  'Joy'       , 'JJJJJJ00J00J000J'),
    ('Kenny'    ,  'Key'       , 'KKKKKK00K00K000K'),
    ('Lino'     ,  'Lini'      , 'LLLLLL00L00L000L'),
    ('Mario'    ,  'Mari'      , 'MMMMMM00M00M000M'),
    ('Nino'     ,  'Noni'      , 'NNNNNN00N00N000N'),
    ('Ottilia'  ,  'Ottoli'    , 'OOOOOO00O00O000O'),
    ('Pippo'    ,  'Poppi'     , 'PPPPPP00P00P000P'),
    ('Quinto'   ,  'Quarti'    , 'QQQQQQ00Q00Q000Q'),
    ('Rino'.    ,  'Rana'      , 'RRRRRR00R00R000R'),
    ('Sandro'   ,  'Sandrini'  , 'SSSSSS00S00S000S'),
    ('Tina'     ,  'Tini'      , 'TTTTTT00T00T000T'),
    ('Ugo'.     ,  'Ughi'      , 'UUUUUU00U00U000U'),
    ('Veronica' ,  'Verona'    , 'VVVVVV00V00V000V'),
    ('Willy'    ,  'Wolly'     , 'WWWWWW00W00W000W'),
    ('Xena'     ,  'Xiao'      , 'XXXXXX00X00X000X'),
    ('Yoghy'    ,  'Yoyo'      , 'YYYYYY00Y00Y000Y'),
    ('Zita'     ,  'Ziti'      , 'ZZZZZZ00Z00Z000Z');


INSERT INTO utenti (id_docente, email, password) 
VALUES 
    (1  ,  'tizio@mail.it'     ,    '$2y$10$xcCQhv7UxuNdLjJyg8SffuwfmCDISjJqBE.WtPwRbevyB7Mhdu9JO'),
    (2  ,  'caio@mail.it'      ,    '$2y$10$K3vHNjBcmpC5Xu/BbDCPou1Qsc3Lyd09VwLo0ing2yZDkXjuEidhq'),
    (3  ,  'sempronio@mail.it' ,    '$2y$10$JSXw4xSZdi9UUcrKwMG/TuFrW0P5AReq1Jpekl0H8WgcgjvNy7q3W'),
    (4  ,  'pinco@mail.it'     ,    '$2y$10$0qnOOo0Lc4qOxjM8eLNzNOR4V9p2p.LYdLfnhgUaDObFJJMUkGaBC'),
    (5  ,  'pino@mail.it'      ,    '$2y$10$fWJz8AYtcd2QJ.3pxuUlFuPPfXGLhYCZF86VqH9zwj5OR0zp85Hmi'),
    (6  ,  'mickey@mail.it'    ,    '$2y$10$GN10MOCH4aaW5o/PQMHCuuw2U5AvQwrs4neoK7M/uCtced2fE2qvG'),
    (7  ,  'donald@mail.it'    ,    '$2y$10$3eQX1LCItYUEqxahJ4MXXeIk7xlvte4xnK9BrgAdSmFz03.ico7We'),
    (8  ,  'daisy@mail.it'     ,    '$2y$10$fUNzCjSZZJOkUovtdwDrjeI.x3DISnr3Z0tscMAApCHanCAuY57lS'),
    (9  ,  'pippo@mail.it'     ,    '$2y$10$5bcJ5BTPRt/T9e6Sa5OgWOW4WuMzzlC6/mp2d/nm1.uRmdNKJtfGi'),
    (10 ,  'silvestro@mail.it' ,    '$2y$10$CNiLLzlvNGIMIHF.2Lset.UxX.yvPt3bLi9nFBZVuf8CH1JPEyOhy');
    
INSERT INTO categorie (nome) 
VALUES 
    (	'Informatica'     ), 
    (	'Economia'        ), 
    (   'Grafica'         ), 
    (	'Amministrazione' ), 
    (	'Lingue'          );


INSERT INTO corsi (titolo, id_categoria) 
VALUES 
    ('Contabilità_e_bilancio'       ,  2),
    ('Programmazione_Java'          ,  1),
    ('Programmazione_Python'        ,  1),
    ("Segretaria_d'_azienda"        ,  4),
    ('Segretaria_di_studio_medico'  ,  4),
    ('Controllo_di_gestione'        ,  2),
    ('Web_design'                   ,  3),
    ('Inglese'                      ,  5),
    ('Spagnolo'                     ,  5),
    ('Francese'                     ,  5);
    
    
INSERT INTO giorni_festivi (data_festiva) 
VALUES
    ('2022-01-01'),
    ('2022-06-01'), 
    ('2022-04-18'),
    ('2022-04-25'),
    ('2022-05-01'),
    ('2022-06-02'),
    ('2022-08-15'),
    ('2022-11-01'),
    ('2022-12-08'),
    ('2022-12-25'),
    ('2022-12-26'),
    ('2023-01-01'),
    ('2023-06-01'), 
    ('2023-04-09'),
    ('2023-04-25'),
    ('2023-05-01'),
    ('2023-06-02'),
    ('2023-08-15'),
    ('2023-11-01'),
    ('2023-12-08'),
    ('2023-12-25'),
    ('2023-12-26');
    
-- argomenti: id_titolo, data_inizio, data_fine, fascia am (bool), fascia pm (bool)
CALL insert_corso(1,  '2023-11-01', '2023-11-30', true, false);
CALL insert_corso(2,  '2023-11-01', '2023-12-31', true,  true);
CALL insert_corso(3,  '2023-11-01', '2024-01-10', false, true);
CALL insert_corso(4,  '2023-12-01', '2024-01-31', true,  true);
CALL insert_corso(5,  '2023-12-01', '2024-02-15', true, false);
CALL insert_corso(6,  '2023-12-15', '2024-02-15', true,  true);
CALL insert_corso(7,  '2023-12-15', '2024-02-28', false, true);
CALL insert_corso(8,  '2024-01-15', '2024-02-28', true,  true);
CALL insert_corso(9,  '2024-01-31', '2024-03-31', false, true);
CALL insert_corso(10, '2024-02-28', '2024-03-31', true, false);


-- argomenti: id_corso, set di id corsisti separati da virgola
CALL insert_corsi_per_corsista(11, '1,2,3,4,5');
CALL insert_corsi_per_corsista(12, '6,7,8,9');
CALL insert_corsi_per_corsista(13, '10,11,12,13');
CALL insert_corsi_per_corsista(14, '14,15,16,17');
CALL insert_corsi_per_corsista(15, '18,19,20,21');
CALL insert_corsi_per_corsista(16, '22,23,24,25,26');
CALL insert_corsi_per_corsista(17, '1,2,3,4,5');
CALL insert_corsi_per_corsista(18, '8,9,10,11,12,13');
CALL insert_corsi_per_corsista(19, '14,15,16,17');
CALL insert_corsi_per_corsista(20, '21,22,23,24,25,26');


-- argomenti: id_corso, set di id docenti separati da virgola
CALL insert_corsi_per_docente(11, '1');
CALL insert_corsi_per_docente(12, '2,4');
CALL insert_corsi_per_docente(13, '3');
CALL insert_corsi_per_docente(14, '5');
CALL insert_corsi_per_docente(15, '4');
CALL insert_corsi_per_docente(16, '6,7');
CALL insert_corsi_per_docente(17, '6');
CALL insert_corsi_per_docente(18, '8,3');
CALL insert_corsi_per_docente(19, '9');
CALL insert_corsi_per_docente(20, '10');


-- argomenti: id_corso, id_docente, data_inizio, data_fine, mattina (bool), pomeriggio (bool)
CALL insert_lezioni_per_docente(11, 1, '2023-11-01',  '2023-11-30', true, false);
CALL insert_lezioni_per_docente(12, 2, '2023-11-01',  '2023-12-31', true, false);
CALL insert_lezioni_per_docente(12, 4, '2023-11-01',  '2023-12-31', false, true);
CALL insert_lezioni_per_docente(13, 3, '2023-11-01',  '2024-01-10', false, true);
CALL insert_lezioni_per_docente(14, 5, '2023-12-01',  '2024-01-31', true,  true);
CALL insert_lezioni_per_docente(15, 4, '2023-12-01',  '2024-02-15', true, false);
CALL insert_lezioni_per_docente(16, 6, '2023-12-15',  '2024-02-15', true, false);
CALL insert_lezioni_per_docente(16, 7, '2023-12-15',  '2024-02-15', false, true);
CALL insert_lezioni_per_docente(17, 6, '2023-12-15',  '2024-02-28', false, true);
CALL insert_lezioni_per_docente(18, 8, '2024-01-15',  '2024-02-28', false, true);
CALL insert_lezioni_per_docente(18, 3, '2024-01-15',  '2024-02-28', true, false);
CALL insert_lezioni_per_docente(19, 9, '2024-01-31',  '2024-03-31', false, true);
CALL insert_lezioni_per_docente(20, 10, '2024-02-28', '2024-03-31', true, false);
