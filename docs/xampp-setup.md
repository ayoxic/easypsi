# EasyPsi avec XAMPP

## 1. Demarrer XAMPP

Lancer `Apache` et `MySQL` depuis le panneau XAMPP.

## 2. Creer la base dans phpMyAdmin

Ouvrir [http://localhost/phpmyadmin](http://localhost/phpmyadmin), puis creer une base :

- nom : `easypsi`
- interclassement : `utf8mb4_unicode_ci`

## 3. Configuration Laravel

Le fichier `.env` du projet est deja prepare pour XAMPP :

```env
APP_URL=http://localhost/easypsi-laravel/public
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=easypsi
DB_USERNAME=root
DB_PASSWORD=
```

Si votre compte MySQL a un mot de passe, remplacez `DB_PASSWORD=` par la bonne valeur.
Si vous utilisez un VirtualHost, remplacez `APP_URL` par votre vraie URL, par exemple `http://easypsi.local`.

## 4. Placement du projet

Option simple :

1. Copier le dossier du projet dans `C:\xampp\htdocs\easypsi-laravel`
2. Ouvrir ensuite le site via `http://localhost/easypsi-laravel/public`

Option propre :

1. Configurer un VirtualHost Apache dont le `DocumentRoot` pointe vers le dossier `public`
2. Utiliser ensuite une URL propre du type `http://easypsi.local`

## 5. Initialiser la base

Depuis le dossier du projet, lancer :

```powershell
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan migrate:fresh --seed
```

La configuration locale utilise aussi des drivers plus simples pour XAMPP :

- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`

## 6. Comptes crees automatiquement

- Admin : `admin@easypsi.test` / `Admin12345!`
- Test : `test@example.com` / `Test12345`

## 7. Commandes utiles

Verifier que tout va bien :

```powershell
C:\xampp\php\php.exe artisan test
```

Si vous modifiez `.env` :

```powershell
C:\xampp\php\php.exe artisan optimize:clear
```
