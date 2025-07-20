# Portail Scolaire - API Documentation

## 🚀 Vue d'ensemble

API RESTful pour la gestion d'un portail scolaire développée avec Laravel 9 et PostgreSQL.


# Configuration la base de données PostgreSQL
env:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=portail
DB_USERNAME=postgres
DB_PASSWORD=passer
```


## 🔐 Authentification

L'API utilise Laravel Sanctum pour l'authentification par token.


## 📚 Documentation des Endpoints

# Authentification

# Inscription
http

POST http://127.0.0.1:8000/api/register

body : json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
}


#### Connexion
http
POST http://127.0.0.1:8000/api/login
body : json

{
    "email": "john@example.com",
    "password": "password123"
}


# Déconnexion
http
POST http://127.0.0.1:8000/api/logout
Authorization: Bearer {mettre le token}



#### Lister tous les élèves
http
GET http://127.0.0.1:8000/api/eleves
Authorization: Bearer {token}


#### Créer un élève
```http
POST http://127.0.0.1:8000/api/eleves
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "Dupont",
    "prenom": "Marie",
    "date_naissance": "2005-03-15",
    "adresse": "123 Rue de la Paix",
    "telephone": "0123456789",
    "email": "marie.dupont@email.com",
    "classe_id": 1
}
```

#### Obtenir un élève
```http
GET http://127.0.0.1:8000/api/eleves/{id}
Authorization: Bearer {token}
```

#### Modifier un élève
```http
PUT http://127.0.0.1:8000/api/eleves/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "Dupont",
    "prenom": "Marie-Claire",
    "date_naissance": "2005-03-15",
    "adresse": "456 Avenue des Fleurs",
    "telephone": "0123456789",
    "email": "marie.dupont@email.com",
    "classe_id": 1
}
```

#### Supprimer un élève
```http
DELETE http://127.0.0.1:8000/api/eleves/{id}
Authorization: Bearer {token}
```

---

### 🏫 Gestion des Classes

#### Lister toutes les classes
```http
GET http://127.0.0.1:8000/api/classes
Authorization: Bearer {token}
```

#### Créer une classe
```http
POST http://127.0.0.1:8000/api/classes
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "6ème A",
    "niveau": "6ème",
    "capacite": 30,
    "annee_scolaire": "2024-2025"
}
```

#### Obtenir une classe
```http
GET http://127.0.0.1:8000/api/classes/{id}
Authorization: Bearer {token}
```

#### Modifier une classe
```http
PUT http://127.0.0.1:8000/api/classes/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "6ème A",
    "niveau": "6ème",
    "capacite": 35,
    "annee_scolaire": "2024-2025"
}
```

#### Supprimer une classe
```http
DELETE http://127.0.0.1:8000/api/classes/{id}
Authorization: Bearer {token}
```

---

### 👨‍🏫 Gestion des Enseignants

#### Lister tous les enseignants
```http
GET http://127.0.0.1:8000/api/enseignants
Authorization: Bearer {token}
```

#### Créer un enseignant
```http
POST http://127.0.0.1:8000/api/enseignants
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "Martin",
    "prenom": "Pierre",
    "email": "pierre.martin@ecole.com",
    "telephone": "0987654321",
    "specialite": "Mathématiques",
    "date_embauche": "2020-09-01"
}
```

#### Obtenir un enseignant
```http
GET http://127.0.0.1:8000/api/enseignants/{id}
Authorization: Bearer {token}
```

#### Modifier un enseignant
```http
PUT http://127.0.0.1:8000/api/enseignants/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "Martin",
    "prenom": "Pierre",
    "email": "pierre.martin@ecole.com",
    "telephone": "0987654321",
    "specialite": "Mathématiques et Physique",
    "date_embauche": "2020-09-01"
}
```

#### Supprimer un enseignant
```http
DELETE http://127.0.0.1:8000/api/enseignants/{id}
Authorization: Bearer {token}
```

---

### 📚 Gestion des Matières

#### Lister toutes les matières
```http
GET http://127.0.0.1:8000/api/matieres
Authorization: Bearer {token}
```

#### Créer une matière
```http
POST http://127.0.0.1:8000/api/matieres
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "Mathématiques",
    "description": "Cours de mathématiques niveau collège",
    "coefficient": 4,
    "niveau": "6ème"
}
```

#### Obtenir une matière
```http
GET http://127.0.0.1:8000/api/matieres/{id}
Authorization: Bearer {token}
```

#### Modifier une matière
```http
PUT http://127.0.0.1:8000/api/matieres/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
    "nom": "Mathématiques",
    "description": "Cours de mathématiques niveau collège et lycée",
    "coefficient": 4,
    "niveau": "6ème"
}
```

#### Supprimer une matière
```http
DELETE http://127.0.0.1:8000/api/matieres/{id}
Authorization: Bearer {token}
```

---

### 📝 Gestion des Notes

#### Lister toutes les notes
```http
GET http://127.0.0.1:8000/api/notes
Authorization: Bearer {token}
```

#### Créer une note
```http
POST http://127.0.0.1:8000/api/notes
Content-Type: application/json
Authorization: Bearer {token}

{
    "eleve_id": 2,
    "matiere_id": 3,
    "enseignant_id": 1,
    "valeur": 15.50,
    "periode": "Trimestre 1",
    "type_evaluation": "Contrôle",
    "commentaire": "Bon travail, continuez comme ça !"
}
```

#### Obtenir une note
```http
GET http://127.0.0.1:8000/api/notes/{id}
Authorization: Bearer {token}
```

#### Modifier une note
```http
PUT http://127.0.0.1:8000/api/notes/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
    "eleve_id": 1,
    "matiere_id": 1,
    "note": 16.0,
    "coefficient": 1,
    "type_evaluation": "Contrôle",
    "date_evaluation": "2024-01-15",
    "commentaire": "Excellent travail"
}
```

#### Supprimer une note
```http
DELETE http://127.0.0.1:8000/api/notes/{id}
Authorization: Bearer {token}
```

---

## 🧪 Tests avec Postman

### Configuration Postman

1. **Variables d'environnement :**
   - `base_url` : `http://127.0.0.1:8000`
   - `token` : (vide, sera rempli après login)

2. **Script de test pour le login :**
```javascript
if (pm.response.code === 200) {
    const response = pm.response.json();
    if (response.data && response.data.token) {
        pm.environment.set("token", response.data.token);
    }
}
```

### Ordre de test recommandé

1. **Register** → Créer un compte
2. **Login** → Obtenir le token
3. **Classes** → Créer des classes
4. **Enseignants** → Créer des enseignants
5. **Matières** → Créer des matières
6. **Élèves** → Créer des élèves
7. **Notes** → Créer des notes
8. **Tests GET** → Lister toutes les données
9. **Tests PUT** → Modifier les données
10. **Tests DELETE** → Supprimer les données
11. **Logout** → Se déconnecter

---

## 📊 Structure des Réponses

### Réponse de succès
```json
{
    "success": true,
    "message": "Opération réussie",
    "data": {
        // Données de la réponse
    }
}
```

### Réponse d'erreur
```json
{
    "success": false,
    "message": "Message d'erreur",
    "errors": {
        "field": ["Messages de validation"]
    }
}
```

---

## 🔒 Sécurité

- **Authentification** : Laravel Sanctum
- **Validation** : Validation Laravel
- **Autorisation** : Middleware de rôle
- **CORS** : Configuration dans `config/cors.php`

---

## 🚀 Déploiement

1. **Configuration serveur**
2. **Variables d'environnement**
3. **Base de données de production**
4. **HTTPS obligatoire**

---

## 📞 Support

Pour toute question ou problème :
- Créez une issue sur GitHub
- Contactez l'équipe de développement

---

## 📄 Licence

[Spécifiez votre licence]

## **📦 Télécharger tous les bulletins d'une classe en ZIP**

**Route :** `POST /api/bulletins-groupe`  
**Méthode :** POST  
**Authentification :** Bearer Token requis  
**Accès :** Administrateurs uniquement

### **Headers :**
```
Content-Type: application/json
Authorization: Bearer {{token}}
Accept: application/zip
```

### **Body (JSON) :**
```json
{
    "classe_id": 1,
    "periode": "Trimestre 1"
}
```

### **Réponse :**
- **Succès :** Fichier ZIP téléchargé automatiquement
- **Nom du fichier :** `bulletins_[NomClasse]_[Periode].zip`
- **Contenu :** Un PDF par élève de la classe

### **Test Postman :**
1. Créez une nouvelle requête POST
2. URL : `{{base_url}}/api/bulletins-groupe`
3. Headers : Ajoutez `Accept: application/zip`
4. Body : Sélectionnez "raw" et "JSON"
5. Entrez le JSON ci-dessus
6. Envoyez la requête
7. Le fichier ZIP se télécharge automatiquement

### **Exemple de contenu du ZIP :**
```
bulletins_6èmeA_Trimestre1.zip
├── bulletin_Dupont_Jean_Trimestre1.pdf
├── bulletin_Martin_Sophie_Trimestre1.pdf
├── bulletin_Bernard_Pierre_Trimestre1.pdf
└── ...
```

---

## 📧 **Notifications Email**

### **Configuration Email**

Ajoutez ces paramètres dans votre fichier `.env` :

```env
# Configuration pour les tests (emails enregistrés dans les logs)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@portail-scolaire.com
MAIL_FROM_NAME="Portail Scolaire"

# Configuration pour la production (exemple avec Gmail)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=votre-email@gmail.com
# MAIL_PASSWORD=votre-mot-de-passe-app
# MAIL_ENCRYPTION=tls
```

### **Notifier la disponibilité d'un bulletin**

**Route :** `POST /api/bulletins/{eleve_id}/notifier/{periode}`  
**Méthode :** POST  
**Authentification :** Bearer Token requis  
**Accès :** Administrateurs, Enseignants, Élèves (seulement leur bulletin)

### **Headers :**
```
Content-Type: application/json
Authorization: Bearer {{token}}
```

### **Paramètres URL :**
- `eleve_id` : ID de l'élève
- `periode` : Période du bulletin (ex: "Trimestre 1 2024")

### **Réponse de succès :**
```json
{
    "message": "Notification envoyée avec succès",
    "eleve": "Dupont Jean",
    "periode": "Trimestre 1 2024",
    "email": "jean.dupont@email.com"
}
```

### **Test avec Postman :**
1. Créez une nouvelle requête POST
2. URL : `{{base_url}}/api/bulletins/1/notifier/Trimestre%201%202024`
3. Headers : Ajoutez votre token Bearer
4. Envoyez la requête

### **Test avec Postman :**
1. Créez une nouvelle requête POST
2. URL : `{{base_url}}/api/bulletins/1/notifier/Trimestre%201%202024`
3. Headers : Ajoutez votre token Bearer
4. Envoyez la requête

### **Vérification des emails :**
- **Mode test :** Consultez `storage/logs/laravel.log`
- **Mode production :** Vérifiez la boîte email de l'élève

### **Template Email :**
Les notifications utilisent le template `resources/views/emails/bulletin-disponible.blade.php` avec :
- Design professionnel et responsive
- Informations sur l'élève et la période
- Lien direct vers le téléchargement du PDF
- Instructions de confidentialité

---

## 🧪 **Tests et Démonstration**

### **Tests disponibles :**
- Utilisez Postman pour tester toutes les API
- Tests unitaires et fonctionnels inclus

### **Données de test recommandées :**
1. Créez un utilisateur admin
2. Créez une classe
3. Créez un élève avec email valide
4. Créez des matières et enseignants
5. Ajoutez des notes
6. Testez la génération de bulletins
7. Testez les notifications email avec Postman

---

## 📞 Support

Pour toute question ou problème :
- Créez une issue sur GitHub
- Contactez l'équipe de développement

---

## 📄 Licence

[Spécifiez votre licence]
