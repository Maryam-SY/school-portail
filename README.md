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



## 🔒 Sécurité

- **Authentification** : Laravel Sanctum
- **Validation** : Validation Laravel
- **Autorisation** : Middleware de rôle
- **CORS** : Configuration dans `config/cors.php`

