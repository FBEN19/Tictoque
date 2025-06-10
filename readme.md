# Trigger `supprimer_doublon_commentaire`

## Description

Le trigger `supprimer_doublon_commentaire` est utilisé pour empêcher l’insertion de commentaires en double dans la table `commentaire`. Il vérifie qu’un utilisateur ne poste pas plusieurs fois le **même commentaire texte** sur la **même recette**.

## Fonctionnement

Ce trigger est exécuté **avant chaque insertion (`BEFORE INSERT`)** dans la table `commentaire`.  
Il vérifie si un commentaire identique (même `id_utilisateur`, `id_recette` et `texte`) existe déjà.  
Si un doublon est détecté, l’insertion est bloquée et une erreur est levée.

## Code SQL

```sql
DELIMITER //

CREATE TRIGGER supprimer_doublon_commentaire
BEFORE INSERT ON commentaire
FOR EACH ROW
BEGIN
    DECLARE existe INT;

    SELECT COUNT(*) INTO existe
    FROM commentaire
    WHERE id_utilisateur = NEW.id_utilisateur
      AND id_recette = NEW.id_recette
      AND texte = NEW.texte;

    IF existe > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Doublon de commentaire trouvé';
    END IF;
END;
//

DELIMITER ;