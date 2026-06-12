# LogiTrans Backend

Sistema de gestión logística basado en arquitectura de microservicios desarrollado con PHP, Slim Framework y Eloquent ORM.

## Tecnologías utilizadas

* PHP 8+
* Slim Framework 4
* Eloquent ORM
* MySQL
* Composer
* JWT

## Estructura del proyecto

```
logitrans-backend/
├── ms-auth
├── ms-conductores
├── ms-vehiculos
├── ms-rutas
├── ms-viajes
└── database
```

## Bases de datos

Importar los scripts SQL ubicados en:

```
database/
```

Bases incluidas:

* ms_auth_db
* ms_conductores_db
* ms_vehiculos_db
* ms_rutas_db
* ms_viajes_db

## Instalación

Ingresar a cada microservicio:

```
cd ms-auth
composer install
```

Configurar el archivo:

```
.env
```

Ejecutar:

```
php -S localhost:8000 -t public
```

## Puertos utilizados

| Microservicio  | Puerto |
| -------------- | ------ |
| ms-auth        | 8000   |
| ms-conductores | 8001   |
| ms-vehiculos   | 8002   |
| ms-rutas       | 8003   |
| ms-viajes      | 8004   |

## Autor

Edwin
