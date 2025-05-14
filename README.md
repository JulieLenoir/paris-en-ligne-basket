# paris-en-ligne-basket

# PARIBASKET – Application de Paris Sportifs Fictifs sur la NBA
# paris-en-ligne-basket
 **PARIBASKET** est une application web dédiée aux paris fictifs sur les matchs NBA. Elle permet aux utilisateurs de : - consulter les statistiques des équipes et joueurs NBA (via l’API SportRadar), - simuler des matchs, - parier virtuellement et gérer leur solde.  Ce projet a été réalisé dans un contexte pédagogique.

## Objectifs pédagogiques

- Appliquer les principes de développement web full-stack (Symfony + Bootstrap)
- Intégrer une API externe (SportRadar)
- Simuler une architecture modulaire et sécurisée
- Mettre en œuvre une base de données relationnelle (MySQL)
- Déployer l’application via Docker et suivre une démarche DevOps

---

## Stack technique

### Backend
- **PHP 8+** avec **Symfony**
- Architecture MVC
- API SportRadar pour les données NBA
- Authentification sécurisée

### Frontend
- HTML / CSS / Bootstrap
- Affichage des matchs, paris, résultats
- Gestion dynamique du portefeuille virtuel

### Base de données
- **MySQL**
- Migrations avec Doctrine
- Relations entre utilisateurs, paris, matchs

### DevOps
- **Docker** pour la containerisation
- Fichiers `docker-compose.yaml` pour l’orchestration
- Environnements `.env` pour config locale et test

---

## Scénarios de tests

Nous avons mis en place **trois tests** couvrant différents aspects de l’application Symfony.

### Tests unitaires

- `UserTest` : teste la logique métier de la classe `User`
  - Détection d’e-mails déjà utilisés
  - Hachage des mots de passe
  - Utilisation de **mocks** pour isoler les dépendances

- `ParisServiceTest` : vérifie la mise à jour automatique des paris après un match
  - Scénarios gagnants / perdants simulés
  - Teste la logique de validation et mise à jour des gains

### Test fonctionnel

- `AuthenticationTest` : simule des requêtes HTTP
  - Vérifie la redirection vers la page de connexion
  - Accès sécurisé aux pages après authentification

L’objectif est de garantir la **robustesse** de chaque composant métier et de l’interface utilisateur.

---

## Déploiement avec Docker

### Conteneurisation de l’application

L’environnement est entièrement conteneurisé pour faciliter le déploiement et la portabilité :

1. **Docker installé localement**
2. Création de dossiers distincts pour le backend et le frontend
3. **Dockerfile Backend** basé sur l’image `php:8.2-fpm`
   - Ajout d’extensions nécessaires : `pdo_mysql`, `git`, etc.
   - Réglage des permissions d’accès à l’arborescence
   - Exposition du port **9000** pour exécuter le backend

### Lancement avec Docker Compose

```bash
docker compose up -d --build
http://localhost:8000

