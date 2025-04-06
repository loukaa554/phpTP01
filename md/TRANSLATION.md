# Traduction PHP Symfony

## Utiliser le composant translator dans le template

```twig
<h1>Ma Super Boutique</h1>
```

```twig
<h1>{{ 'app.title'|trans }}</h1>
```

```bash
# Créer le fichier de traduction
php bin/console translation:extract fr --force
```

```xml
<source>Default.index.hello</source>
<target>Bonjour {nom} !</target>
```

```twig
{# templates/Default/index.html.twig #}
{{ ‘Default.index.hello’| trans({'{nom}': name}) }}
```

```yaml
# config/packages/translation.yaml
framework:
    default_locale: 'fr'
    translator:
        default_path: '%kernel.project_dir%/translations’
        fallbacks: ['fr']
```

```yaml
# config/services.yaml
parameters:
  app.supported_locales: "fr|en"
```

```php
// src/Controller/DefaultController.php
namespace App\Controller;
// ...
class DefaultController extends AbstractController {
    #[Route(
        path: '/{_locale}'
        name: 'app_default_index',
        requirements: ['_locale' => '%app.supported_locales%'],
        defaults: ['_locale' => 'fr']
    )]
    public function index() {
        // ...
    }

    #[Route(
        path: '/{_locale}/contact',
        name: 'app_default_contact',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function contact() {
    // ...
    }
}
```
