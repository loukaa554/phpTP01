# Autres PHP Symfony

## Moyenne du note d'un produit

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
