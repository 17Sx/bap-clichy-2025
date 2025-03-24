# 🍽️ BAP Clichy 2025 - Plateforme de Réduction du Gaspillage Alimentaire

## 📝 À propos du projet

BAP Clichy 2025 est une plateforme web innovante visant à réduire le gaspillage alimentaire dans les écoles de Clichy en facilitant la redistribution des surplus alimentaires aux associations caritatives.

### 🎯 Objectif

Notre mission est de créer un pont entre les établissements scolaires et les associations caritatives pour optimiser la gestion des surplus alimentaires et contribuer à la réduction du gaspillage alimentaire.

## 👥 Équipe de développement

Ce projet a été développé par :

- **17Sx** - Développeur
- **Yohzenn** - Développeur

## 🛠️ Technologies utilisées

- **Backend** : PHP natif
- **Base de données** : MySQL
- **Frontend** : HTML5, CSS3, JavaScript
- **APIs** : Google Maps

## 🔑 Configuration des clés API

### Google Maps API

1. **Obtenir une clé API** :

   - Allez sur [Google Cloud Console](https://console.cloud.google.com/)
   - Créez un compte si vous n'en avez pas
   - Créez un nouveau projet ou sélectionnez un projet existant
   - Dans le menu, allez dans "APIs & Services" > "Credentials"
   - Cliquez sur "Create Credentials" > "API key"
   - Votre clé API sera générée

2. **Sécuriser votre clé API** :

   - Dans Google Cloud Console, allez dans "APIs & Services" > "Credentials"
   - Cliquez sur votre clé API
   - Dans "Application restrictions", choisissez "HTTP referrers"
   - Ajoutez votre domaine (ex: `*.votredomaine.com/*`)
   - Dans "API restrictions", sélectionnez "Maps JavaScript API"

3. **Configurer le projet** :
   - Copiez le fichier `config/config.example.php` vers `config/config.php`
   - Remplacez `VOTRE_CLE_API_ICI` par votre clé API Google Maps
   - Assurez-vous que le fichier `config.php` est bien ignoré par Git (vérifiez `.gitignore`)

## 🌟 Fonctionnalités principales

### 👨‍🍳 Pour les établissements scolaires

- Publication des surplus alimentaires
- Gestion des stocks en temps réel
- Suivi des demandes d'associations
- Interface d'administration intuitive

### 🤝 Pour les associations

- Consultation des disponibilités
- Système de réservation
- Gestion des commandes
- Suivi des récupérations

### 👨‍💼 Pour les administrateurs

- Modération des annonces
- Gestion des utilisateurs
- Suivi des statistiques
- Contrôle des transactions

## 🚀 Installation

1. Cloner le repository

```bash
git clone https://github.com/votre-username/bap-clichy-2025.git
```

2. Configurer la base de données

- Créer une base de données MySQL
- Importer le fichier `database.sql`
- Configurer les paramètres de connexion dans `config.php`

3. Configurer le serveur web

- Placer les fichiers dans le répertoire de votre serveur web
- S'assurer que PHP et MySQL sont installés
- Configurer les permissions des dossiers

## 📋 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Extensions PHP requises :
  - PDO
  - PDO_MySQL
  - GD
  - JSON

## 📱 Interface utilisateur

- Design responsive
- Navigation intuitive
- Filtres de recherche avancés
- Système de tags pour les catégories alimentaires
- Interface d'administration complète

<div align="center">
  <p>Développé avec ❤️ pour la réduction du gaspillage alimentaire</p>
</div>
