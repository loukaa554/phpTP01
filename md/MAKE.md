# Make PHP

---

## Migration

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

```bash
php bin/console doctrine:migrations:diff
```

---

## Création d'un controller

```bash
php bin/console make:controller
```

## Création d'un entity

```bash
php bin/console make:entity
```

## Création d'un repository

```bash
php bin/console make:entity --regenerate
```

## Création d'un form

```bash
php bin/console make:form CommentaireType
```

## Création d'un migration

```bash
php bin/console make:migration
```

## Création d'un fixture

```bash
php bin/console make:fixtures
```

## Création d'un authentification

```bash
php bin/console make:auth
```

## Création d'un crud

```bash
php bin/console make:crud Produit
```

## Création d'un event subscriber

```bash
php bin/console make:subscriber
```

## Création d'un event listener

```bash
php bin/console make:listener
```

## Création d'un command

```bash
php bin/console make:command
```

## Création d'un service

```bash
php bin/console make:service
```

## Création d'un mailer

```bash
php bin/console make:mailer
```

## Création d'un test

```bash
php bin/console make:test
```

## Création d'un translation

```bash
php bin/console make:translation
```

## Création d'un translation extractor

```bash
php bin/console make:translation-extractor
```
