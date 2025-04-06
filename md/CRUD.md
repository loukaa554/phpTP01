# 🛠️ Mise en place du Back-Office (CRUD) dans Symfony pour `Categorie` et `Produit`

## ✅ Étape 1 : Génération des interfaces CRUD

Utilise la commande suivante pour générer automatiquement les interfaces CRUD avec Symfony :

### Pour l'entité `Categorie` :

```bash
php bin/console make:crud Categorie
```

### Pour l'entité `Produit` :

```bash
php bin/console make:crud Produit
```

Cela génère :

- Un contrôleur (`CategorieController.php`, `ProduitController.php`)
- Un dossier de templates dans `templates/categorie/` et `templates/produit/`
- Un formulaire (`CategorieType.php`, `ProduitType.php`)

## ✅ Étape 2 : Sécuriser les accès aux pages CRUD

### Méthode recommandée : Annotation sur la classe

Dans chaque contrôleur CRUD, ajoute cette annotation :

```php
#[IsGranted('ROLE_ADMIN')]
```

**Exemple dans** `CategorieController.php :

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class CategorieController extends AbstractController
{
    // toutes les méthodes ici seront réservées aux admins
}
```

## 🧭 Étape 3 : Ajouter un lien vers le Back-Office dans la barre de navigation

Dans le fichier `base.html.twig` :

```twig
{% if is_granted('ROLE_ADMIN') %}
    <li><a href="{{ path('categorie_index') }}">Admin - Catégories</a></li>
    <li><a href="{{ path('produit_index') }}">Admin - Produits</a></li>
{% endif %}

```

## 🛡️ Étape 4 : Sécuriser via security.yaml

Dans le fichier `config/packages/security.yaml`, ajoute les rôles et les accès :

```yaml
access_control:
  - { path: ^/categorie, roles: ROLE_ADMIN }
  - { path: ^/produit, roles: ROLE_ADMIN }
```
