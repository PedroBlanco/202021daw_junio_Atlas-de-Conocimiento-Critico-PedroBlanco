# Atlas de Conocimiento Crítico

[![Licencia EUPLv1.2 o superior](eupl_1.2.svg)](LICENSE)
[![Datos bajo Licencia de Creative Commons](https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png)](http://creativecommons.org/licenses/by-nc-sa/4.0/)

**2023-02-24 - Este proyecto ha sido archivado**

Este es el repositorio del Proyecto Integrado _Atlas de Conocimiento Crítico_ del Ciclo Superior de Formación Profesional _"Desarrollo de Aplicaciones Web"_ del [IES Gran Capitan](https://informatica.iesgrancapitan.org).

El objetivo principal de este proyecto es elaborar un "Directorio de Mapas de Conocimiento Crítico", concretamente con forma de "mapa geolocalizado" de [mapas de conocimiento crítico] y sus datos asociados.

La idea fue propuesta al Grupo de Gestión de Conocimiento [Identificación del Conocimiento Crítico] del Programa "[Embajadores del Conocimiento]" por parte de los coordinadores de dicho Programa, que es una iniciativa del [Instituto Andaluz de Administración Pública] ([Junta de Andalucía]).

El nombre _Atlas de Conocimiento Crítico_ viene por ser un _Directorio_ o compendio de _Mapas de Conocimiento Crítico_ y consiste en una aplicación web desarrollada en [Laravel] y pensada para ser desplegada mediante contenedores [Docker](https://www.docker.com/) de Linux.

## Instalación

La instrucciones detalladas de instalación y despliegue se encuentran en la sección [Manual de Despliegue] dentro del wiki del proyecto.

Para una instalación rápida y accesible desde [http://localhost/] podemos seguir la siguientes instrucciones:

### Requisitos de la instalación rápida

Aparte de tener el puerto 80 libre en nuestra máquina, necesitaremos tener instaladas lo siguiente:

- Sistema operativo Linux con arquitectura ```x86_64 / adm64```.
- Puertos libres en nuestra máquina:
  - 80, 443:
    - Aplicación Atlas.
- 8081: (opcional)
  - Adminer, gestión de la BD (contenedor ```atlas_adminer```).
- 2222: (opcional)
  - Acceso SSH al contenedor ```atlas_workspace```.
- Software:
  - Sistema de contenedores ```docker```:
    - Recomiendo usar la versión ```community``` actualizada disponible desde el [repositorio oficial de Docker](https://docs.docker.com/engine/install/).
  - Shell ```bash```.
  - Sistema de control de versiones ```git```.
  - Comando ```envsubst```:
    - En Debian y derivadas suele estar en el paquete ```gettext-base```

### Instalación y despliegue automáticos

Para desplegar instalar de forma automática la aplicación se recomienda usar el script ```atlas-quick.sh``` que se encuentra en la carpeta raiz del repositorio y al que podemos darle permisos de ejecución o ejecutarlo mediate ```bash```.

Cuenta con las siguientes opciones:

- ```up```: Inicializa la instalación desde el principio, crea los contenedores y configura el entorno.
- ```down```: Elimina los contenedores.
- ```start```: Arranca los contenedores (deben existir).
- ```stop```:  Para los contenedores (siguen existiendo).
- ```restart```: Reinicia los contenedores (a veces hace falta para que recarguen la configuración).
- ```update```: Para los contenedores, reconfigura y monta de nuevo el entorno, y vuelve a iniciar los contenedores.

Primero de todo tenemos que de

```shell
git clone --recurse-submodules --branch main https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco.git atlas
cd atlas
bash atlas-quick.sh up
```

Una vez se ha ejecutado el script, deberíamos poder acceder a la aplicación instalada en [http://localhost/].

### Instalación y despliegue por pasos

Alternativamente, se pueden seguir los siguientes pasos ejecutando en un shell ```bash```:

```shell
~$ git clone --recurse-submodules --branch main https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco.git atlas
~$ cd atlas
~/atlas$ cp atlas/.env.example atlas/.env
~/atlas$ cp laradock-atlas-daw/.env.example laradock-atlas-daw/.env
~/atlas$ cp laradock-atlas-daw/nginx/sites/atlas.conf.example laradock-atlas-daw/nginx/sites/atlas.conf
~/atlas$ cd laradock-atlas-daw
~/atlas/laradock-atlas-daw$ docker-compose up -d
```

Con los contenedores de la aplicación generados y ejecutándose, llega el momento de configurar la base de datos:

```shell
~/atlas$ set -a
~/atlas$ source atlas/.env
~/atlas$ source laradock-atlas-daw/.env
~/atlas$ envsubst < install/atlas-init.sql | docker exec -it atlas_mariadb mysql -u root --password=$MARIADB_ROOT_PASSWORD
```

Ahora es el turno de configurar la parte [Laravel] de la aplicación:

```shell
docker exec -w /var/www/atlas atlas_workspace composer install
docker exec -w /var/www/atlas atlas_workspace npm install
docker exec -w /var/www/atlas atlas_workspace npm run dev
docker exec -w /var/www/atlas atlas_workspace php artisan key:generate
docker exec -w /var/www/atlas atlas_workspace php artisan migrate
docker exec -w /var/www/atlas atlas_workspace php artisan scout:import "App\Models\Autor"
docker exec -w /var/www/atlas atlas_workspace php artisan scout:import "App\Models\Geo"
docker exec -w /var/www/atlas atlas_workspace php artisan scout:import "App\Models\Mapa"
docker exec -w /var/www/atlas atlas_workspace php artisan scout:import "App\Models\Ambito"
docker exec -w /var/www/atlas atlas_workspace php artisan scout:import "App\Models\Administracion"
docker exec -w /var/www/atlas atlas_workspace php artisan scout:import "App\Models\Estado"
docker exec -w /var/www/atlas atlas_workspace php artisan scout:import "App\Models\User"
docker exec -w /var/www/atlas atlas_workspace php artisan db:seed --force
docker exec -w /var/www/atlas atlas_workspace php artisan config:cache
```

Nota: En cada línea, la primera parte es el directorio en que se debe ejecutar cada orden, suponiendo que empecemos por el directorio personal del usuario (```/home/usuario``` o ```~``` de forma abreviada). Si no está presente, es indiferente desde qué directorio se ejecute.

## Documentación

Toda la documentación del proyecto se puede encontrar en la [wiki](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki):

- [Inicio](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/Home)
- [Documentación del Proyecto](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/Doc_PI)
  - [Introducción](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/1Doc_Introduccion)
  - [Objetivos y requisitos del proyecto](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/2Doc_Objetivos_Requisitos)
  - [Estudio previo](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/3Doc_Estudio_previo)
  - [Plan de trabajo](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/4Doc_Plan_Trabajo)
  - [Diseño](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/5Doc_Diseno)
  - [Implantación](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/6Doc_Implantacion)
  - [Recursos](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/7Doc_Recursos)
  - [Conclusiones](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/8Doc_Conclusiones)
  - [Referencias / bibliografía](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/9Doc_Referencias_Bibliografia)
  - [Anexos](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/10Doc_Anexos)
- [Manual de Despliegue](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/Manual_Despliegue)
- [Manual de Usuario](https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/Manual_Usuario)

## Uso

La aplicación viene precargada con los datos de los [mapas de conocimiento crítico] realizados (en diferentes fases de desarrollo) desde que empezó Grupo de Gestión de Conocimiento [Identificación del Conocimiento Crítico]  en el Programa "[Embajadores del Conocimiento]" en la edición 2018/2019 hasta la actualidad.

Además se crea un primer usuario (con perfil de Superadministrador) por defecto:

- Usuario: ```super@example.com```
- Contraseña: ```password```

Si hemos seguido los pasos de instalación rápida descritos anteriormente, podremos entrar en la aplicación con las credenciales anteriores en la dirección [http://localhost/]

## Autor

- [@PedroBlanco](https://www.github.com/PedroBlanco)

## Licencias

### Aplicación Atlas

Esta aplicación está bajo licencia [EUPL versión EUPL-1.2-o-posterior]. Para cualquier aclaración, sírvase visitar el siguiente enlace: [https://joinup.ec.europa.eu/collection/eupl/news/understanding-eupl-v12](https://joinup.ec.europa.eu/collection/eupl/news/understanding-eupl-v12)

### Datos de precarga

Los datos precargados en la base de datos de la aplicación de los Mapas de Conocimiento Crítico, de sus autores y demás información son propiedad del [Instituto Andaluz de Administración Pública] ([Junta de Andalucía]) y no pueden usarse con fines comerciales ni distintos a la formación, divulgación, investigación o demostración.

Estos datos están cubiertos bajo la <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">licencia de Creative Commons Reconocimiento-NoComercial-CompartirIgual 4.0 Internacional</a>.

[Embajadores del Conocimiento]: https://ws168.juntadeandalucia.es/iaap/gestiondelconocimiento/embajadores-del-conocimiento/
[Instituto Andaluz de Administración Pública]: https://www.juntadeandalucia.es/institutodeadministracionpublica/publico/home.filter
[Junta de Andalucía]: https://juntadeandalucia.es/
[http://localhost/]: http://localhost/
[mapas de conocimiento crítico]: https://ws168.juntadeandalucia.es/wikigestionC/index.php?title=Mapa_de_Conocimiento_Cr%C3%ADtico
[Identificación del Conocimiento Crítico]: https://ws168.juntadeandalucia.es/iaap/gestiondelconocimiento/proyectos/edicion-2019-2020-edicion-2019-2020/gc06-identificacion-del-conocimiento-critico/
[Manual de Despliegue]: https://github.com/PedroBlanco/202021daw_junio_Atlas-de-Conocimiento-Critico-PedroBlanco/wiki/Manual_Despliegue
[Laravel]: https://laravel.com/
[EUPL versión EUPL-1.2-o-posterior]: https://joinup.ec.europa.eu/sites/default/files/inline-files/EUPL%20v1_2%20ES.txt
