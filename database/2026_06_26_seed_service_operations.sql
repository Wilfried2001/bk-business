-- Types d'operations issus des donnees historiques TDJ.csv.
-- Ce script evite que l'etape "Type d'operation" soit vide quand aucune commission
-- n'est encore configuree pour un service.

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Envoi client', 'Envoi international client', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Envoi client');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Retrait client', 'Paiement ou retrait international client', 0, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Retrait client');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Retour de fond', 'Retour de fonds ou annulation payee', 0, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Retour de fond');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Annulation', 'Annulation operation internationale', 0, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Annulation');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Depot en especes', 'Depot especes en agence', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Depot en especes');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Cash in float', 'Approvisionnement float', 1, -1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Cash in float');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Cash out float', 'Sortie ou retrait float', -1, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Cash out float');

INSERT INTO type_operation (libelle, description, impact_float, impact_caisse)
SELECT 'Envoi colis', 'Envoi de colis', -1, 1
WHERE NOT EXISTS (SELECT 1 FROM type_operation WHERE libelle = 'Envoi colis');
