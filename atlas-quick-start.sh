#!/usr/bin/env bash

# Descargamos la aplicación del repositorio
git clone --recurse-submodules --branch develop https://github.com/iesgrancapitan-proyectos/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco.git atlas

# Tomamos la configuración por defecto
cd atlas
cp atlas/.env.example atlas/.env
cp laradock-atlas-daw/.env.example laradock-atlas-daw/.env
cp laradock-atlas-daw/nginx/sites/atlas.conf.example laradock-atlas-daw/nginx/sites/atlas.conf

# Generamos y levantamos los contenedores
cd laradock-atlas-daw
docker-compose up -d

# Creamos la base de datos y el usuario que usaremos
cd ..
set -a
source atlas/.env
source laradock-atlas-daw/.env
envsubst < install/atlas-init.sql | docker exec -it atlas_mariadb mysql -u root --password=$MARIADB_ROOT_PASSWORD

# Configuramos la parte Laravel de la aplicación
docker exec -w /var/www/atlas atlas_workspace composer install
docker exec -w /var/www/atlas atlas_workspace npm install
docker exec -w /var/www/atlas atlas_workspace npm run dev
docker exec -w /var/www/atlas atlas_workspace php artisan key:generate
docker exec -w /var/www/atlas atlas_workspace php artisan migrate --seed
docker exec -w /var/www/atlas atlas_workspace php artisan config:cache
