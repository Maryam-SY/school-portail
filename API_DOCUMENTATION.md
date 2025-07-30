# 📚 Documentation API - Dashboard Enseignant

## 🔐 Authentification

Toutes les API nécessitent une authentification avec un token Bearer.

```bash
# Connexion
POST /api/login
{
    "email": "ibrahima@school.com",
    "password": "password123"
}

# Réponse
{
    "access_token": "token_here",
    "token_type": "Bearer",
    "user": {...}
}
```

## 📊 API Dashboard Enseignant

### 1. Statistiques du Dashboard
```bash
GET /api/enseignant/stats
Authorization: Bearer {token}

# Réponse
{
    "nb_classes": 2,
    "nb_matieres": 2,
    "nb_notes": 1,
    "nb_bulletins": 0
}
```

### 2. Classes de l'enseignant
```bash
GET /api/enseignant/mes-classes
Authorization: Bearer {token}

# Réponse
[
    {
        "id": 2,
        "nom": "Master 2 Data Sciences",
        "niveau": "Master 2",
        "capacite": 25,
        "annee_scolaire": "2024-2025"
    },
    {
        "id": 4,
        "nom": "Licence 1 Génie Logiciel",
        "niveau": "Licence 1",
        "capacite": 25,
        "annee_scolaire": "2024-2025"
    }
]
```

### 3. Matières de l'enseignant
```bash
GET /api/enseignant/mes-matieres
Authorization: Bearer {token}

# Réponse
[
    {
        "id": 2,
        "nom": "Java",
        "code": "JAVA",
        "description": "Programmation orientée objet",
        "niveau": "Master 2",
        "coefficient": "3.0"
    },
    {
        "id": 6,
        "nom": "Analyse Merise",
        "code": "AME",
        "description": null,
        "niveau": "Licence 1",
        "coefficient": "3.0"
    }
]
```

### 4. Tous les élèves de l'enseignant
```bash
GET /api/enseignant/eleves
Authorization: Bearer {token}

# Réponse
[
    {
        "id": 2,
        "nom": "Omar",
        "prenom": "Ba",
        "classe": {
            "id": 2,
            "nom": "Master 2 Data Sciences",
            "niveau": "Master 2"
        }
    },
    {
        "id": 4,
        "nom": "Diop",
        "prenom": "assane",
        "classe": {
            "id": 2,
            "nom": "Master 2 Data Sciences",
            "niveau": "Master 2"
        }
    }
]
```

## 🎯 API de Filtrage Avancé

### 1. Élèves par classe
```bash
GET /api/enseignant/classes/{classe_id}/eleves-filtres
Authorization: Bearer {token}

# Exemple
GET /api/enseignant/classes/2/eleves-filtres
```

### 2. Élèves par matière
```bash
GET /api/enseignant/matieres/{matiere_id}/eleves
Authorization: Bearer {token}

# Exemple
GET /api/enseignant/matieres/2/eleves
```

### 3. Élèves par classe ET matière
```bash
GET /api/enseignant/classes/{classe_id}/matieres/{matiere_id}/eleves
Authorization: Bearer {token}

# Exemple
GET /api/enseignant/classes/2/matieres/2/eleves
```

### 4. Matières enseignées dans une classe
```bash
GET /api/enseignant/classes/{classe_id}/matieres
Authorization: Bearer {token}

# Exemple
GET /api/enseignant/classes/2/matieres
```

## 📝 Gestion des Notes

### 1. Lister les notes de l'enseignant
```bash
GET /api/enseignant/notes
Authorization: Bearer {token}
```

### 2. Ajouter une note
```bash
POST /api/notes
Authorization: Bearer {token}
{
    "eleve_id": 2,
    "matiere_id": 2,
    "valeur": 15.5,
    "periode": "Semestre 1"
}
```

### 3. Modifier une note
```bash
PUT /api/notes/{id}
Authorization: Bearer {token}
{
    "valeur": 16.0,
    "periode": "Semestre 1"
}
```

### 4. Supprimer une note
```bash
DELETE /api/notes/{id}
Authorization: Bearer {token}
```

## 🔒 Sécurité et Permissions

### Vérifications automatiques :
- ✅ L'enseignant ne peut voir que ses classes
- ✅ L'enseignant ne peut voir que ses matières
- ✅ L'enseignant ne peut voir que ses élèves
- ✅ L'enseignant ne peut noter que ses élèves dans ses matières
- ✅ Vérification que l'enseignant enseigne la matière dans la classe de l'élève
- ✅ Prévention des notes en double (même élève/matière/période)

### Structure de la table `enseignant_matiere` :
```sql
CREATE TABLE enseignant_matiere (
    id BIGINT PRIMARY KEY,
    enseignant_id BIGINT,
    matiere_id BIGINT,
    classe_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🎯 Cas d'usage Frontend

### 1. Formulaire d'ajout de note
```javascript
// 1. Charger les classes de l'enseignant
GET /api/enseignant/mes-classes

// 2. Quand une classe est sélectionnée, charger ses élèves
GET /api/enseignant/classes/{classe_id}/eleves-filtres

// 3. Charger les matières enseignées dans cette classe
GET /api/enseignant/classes/{classe_id}/matieres

// 4. Ajouter la note
POST /api/notes
```

### 2. Filtrage avancé
```javascript
// Filtrer par classe uniquement
GET /api/enseignant/classes/2/eleves-filtres

// Filtrer par matière uniquement
GET /api/enseignant/matieres/2/eleves

// Filtrer par classe ET matière
GET /api/enseignant/classes/2/matieres/2/eleves
```

## ✅ Statut des API

| API | Statut | Description |
|-----|--------|-------------|
| `/api/enseignant/stats` | ✅ Fonctionnel | Statistiques du dashboard |
| `/api/enseignant/mes-classes` | ✅ Fonctionnel | Classes de l'enseignant |
| `/api/enseignant/mes-matieres` | ✅ Fonctionnel | Matières de l'enseignant |
| `/api/enseignant/eleves` | ✅ Fonctionnel | Tous les élèves |
| `/api/enseignant/notes` | ✅ Fonctionnel | Notes de l'enseignant |
| `/api/notes` (CRUD) | ✅ Fonctionnel | Gestion complète des notes |
| Filtrage avancé | ✅ Fonctionnel | Toutes les API de filtrage |

## 🚀 Prochaines étapes

1. **Frontend** : Mettre à jour les URLs pour utiliser `/api/enseignant/` au lieu de `/api/enseignants/`
2. **Interface** : Implémenter les filtres dans le formulaire d'ajout de note
3. **Validation** : Tester tous les cas d'usage avec les données réelles
4. **UX** : Ajouter des messages d'erreur clairs pour les permissions 