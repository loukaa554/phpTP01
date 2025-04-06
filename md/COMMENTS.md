# 💬 Mise en place des Commentaires et Notes sur un Produit (Symfony)

## 🌟 Objectif

Permettre à un usager authentifié :

- d’ajouter un commentaire (texte non vide) et une note (entier entre 0 et 5) sur un produit.
- d’afficher la note moyenne d’un produit et la liste des commentaires.

---

## ✅ Étape 1 : Création de l’entité `Commentaire`

```bash
php bin/console make:entity Commentaire
```

### Champs à ajouter :

| Champ   | Type     | Contraintes                                 |
| ------- | -------- | ------------------------------------------- |
| texte   | text     | `NotBlank`                                  |
| note    | integer  | `NotNull`, `Range(min=0, max=5)`            |
| usager  | relation | ManyToOne vers `Usager`, `nullable: false`  |
| produit | relation | ManyToOne vers `Produit`, `nullable: false` |

### Exemple dans `Commentaire.php` :

```php
#[ORM\Column(type: 'text')]
#[Assert\NotBlank]
private ?string $texte = null;

#[ORM\Column(type: 'integer')]
#[Assert\NotNull]
#[Assert\Range(min: 0, max: 5)]
private ?int $note = null;

#[ORM\ManyToOne(targetEntity: Usager::class)]
#[ORM\JoinColumn(nullable: false)]
private ?Usager $usager = null;

#[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'commentaires')]
#[ORM\JoinColumn(nullable: false)]
private ?Produit $produit = null;
```

### Ne pas oublier la migration :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## 🔄 Étape 2 : Ajouter la relation dans l'entité `Produit`

Dans `Produit.php`, ajouter :

```php
#[ORM\OneToMany(mappedBy: 'produit', targetEntity: Commentaire::class, orphanRemoval: true)]
private Collection $commentaires;
```

Et la méthode de calcul de la note moyenne :

```php
public function getNoteMoyenne(): ?float
{
    if ($this->commentaires->isEmpty()) {
        return null;
    }

    $total = 0;
    foreach ($this->commentaires as $commentaire) {
        $total += $commentaire->getNote();
    }

    return $total / count($this->commentaires);
}
```

---

## 📓 Étape 3 : Création du formulaire `CommentaireType`

```bash
php bin/console make:form CommentaireType
```

### Exemple de formulaire :

```php
class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('texte', TextareaType::class, [
                'label' => 'Votre commentaire',
            ])
            ->add('note', IntegerType::class, [
                'label' => 'Note sur 5',
            ]);
    }
}
```

---

## 🛍️ Étape 4 : Création du contrôleur `CommentaireController`

```bash
php bin/console make:controller CommentaireController
```

### Exemple de méthode pour ajouter un commentaire :

```php
#[Route('/{_locale}/produit/{id}/commentaire', name: 'commentaire_ajouter')]
public function ajouter(
    Request $request,
    Produit $produit,
    EntityManagerInterface $em,
    Security $security
): Response {
    $commentaire = new Commentaire();
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $commentaire->setProduit($produit);
        $commentaire->setUsager($security->getUser());
        $em->persist($commentaire);
        $em->flush();

        return $this->redirectToRoute('produit_detail', ['id' => $produit->getId()]);
    }

    return $this->render('commentaire/ajouter.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit
    ]);
}
```

---

## 🎨 Étape 5 : Template d’ajout (`commentaire/ajouter.html.twig`)

```twig
<h2>Ajouter un commentaire pour {{ produit.libelle }}</h2>

{{ form_start(form) }}
    {{ form_row(form.texte) }}
    {{ form_row(form.note) }}
    <button class="btn btn-primary">Envoyer</button>
{{ form_end(form) }}
```

---

## 👁️ Étape 6 : Affichage des commentaires sur la page produit

Dans `produit/detail.html.twig` :

```twig
{% if app.user %}
    <a href="{{ path('commentaire_ajouter', { id: produit.id, _locale: app.request.locale }) }}" class="btn btn-success">
        Ajouter un commentaire
    </a>
{% endif %}

<h3>Évaluations</h3>

{% if produit.noteMoyenne is not null %}
    <p>Note moyenne : {{ produit.noteMoyenne|number_format(2) }} / 5</p>
{% else %}
    <p>Aucune évaluation pour l’instant</p>
{% endif %}

{% for commentaire in produit.commentaires %}
    <div class="card my-2">
        <div class="card-body">
            <strong>{{ commentaire.usager.prenom }}</strong> - Note : {{ commentaire.note }}/5
            <p>{{ commentaire.texte }}</p>
        </div>
    </div>
{% endfor %}
```
