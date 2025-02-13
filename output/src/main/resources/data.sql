-- Athletes
INSERT INTO athlete (id, nom, date_naissance) VALUES (1, 'Usain Bolt', '1986-08-21');
INSERT INTO athlete (id, nom, date_naissance) VALUES (2, 'Michael Phelps', '1985-06-30');

-- Coaches
INSERT INTO coach (id, nom, specialite) VALUES (1, 'John Smith', 'Running');
INSERT INTO coach (id, nom, specialite) VALUES (2, 'Mark Johnson', 'Swimming');

-- Performances
INSERT INTO performance (id, type, date, valeur, athlete_id) VALUES (1, '100m', '2023-01-15', 9.58, 1);

-- Objectifs
INSERT INTO objectif (id, description, dateEcheance, atteint, athlete_id) VALUES (1, 'Win Olympic Gold', '2024-07-01', false, 1);
