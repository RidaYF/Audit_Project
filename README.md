# Configuration du projet
composer install 
npm install 

## LANCE LE PROJET

rm database/database.sqlite

## Créer une nouvelle base
rm database/database.sqlite
touch database/database.sqlite

## Exécuter les migrations
php artisan migrate

php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=IsoMasterDataSeeder

## LE STOCKAGE DES IMAGES
php artisan storage:link

