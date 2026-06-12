# LogiTrans Backend

Sistema de gestión logística desarrollado bajo arquitectura de microservicios utilizando PHP, Slim Framework, Eloquent ORM y MySQL.

## Descripción

El sistema permite gestionar:

* Autenticación de usuarios.
* Administración de conductores.
* Administración de vehículos.
* Gestión de rutas.
* Programación de viajes.
* Seguimiento operativo de viajes.

Cada módulo se implementa como un microservicio independiente con su propia base de datos.

---

# Tecnologías utilizadas

* PHP 8+
* Slim Framework 4
* Eloquent ORM
* MySQL
* Composer
* JSON REST API
* Git y GitHub

---

# Arquitectura

El proyecto está dividido en los siguientes microservicios:

| Microservicio  | Descripción                               |
| -------------- | ----------------------------------------- |
| ms-auth        | Gestión de autenticación y sesiones       |
| ms-conductores | Administración de conductores             |
| ms-vehiculos   | Administración de vehículos               |
| ms-rutas       | Gestión de rutas y programación de viajes |
| ms-viajes      | Seguimiento y control operativo de viajes |

---

# Estructura del proyecto

```text
logitrans-backend/
│
├── ms-auth/
├── ms-conductores/
├── ms-vehiculos/
├── ms-rutas/
├── ms-viajes/
│
├── database/
│   ├── ms_auth_db.sql
│   ├── ms_conductores_db.sql
│   ├── ms_vehiculos_db.sql
│   ├── ms_rutas_db.sql
│   └── ms_viajes_db.sql
│
└── README.md
```

---

# Bases de datos

Antes de ejecutar el proyecto se deben importar los scripts SQL ubicados en la carpeta:

```text
database/
```

Bases requeridas:

* ms_auth_db
* ms_conductores_db
* ms_vehiculos_db
* ms_rutas_db
* ms_viajes_db

---

# Instalación

## 1. Clonar repositorio

```bash
git clone URL_DEL_REPOSITORIO
```

---

## 2. Instalar dependencias

Ingresar a cada microservicio:

```bash
cd ms-auth
composer install
```

Repetir para:

```text
ms-conductores
ms-vehiculos
ms-rutas
ms-viajes
```

---

## 3. Configurar variables de entorno

Crear archivo:

```text
.env
```

Ejemplo:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ms_auth_db
DB_USERNAME=root
DB_PASSWORD=
```

Cada microservicio debe apuntar a su propia base de datos.

---

# Ejecución

## ms-auth

```bash
php -S localhost:8000 -t public
```

## ms-conductores

```bash
php -S localhost:8001 -t public
```

## ms-vehiculos

```bash
php -S localhost:8002 -t public
```

## ms-rutas

```bash
php -S localhost:8003 -t public
```

## ms-viajes

```bash
php -S localhost:8004 -t public
```

---

# Puertos utilizados

| Servicio       | Puerto |
| -------------- | ------ |
| ms-auth        | 8000   |
| ms-conductores | 8001   |
| ms-vehiculos   | 8002   |
| ms-rutas       | 8003   |
| ms-viajes      | 8004   |

---

# Funcionalidades implementadas

## Autenticación

* Inicio de sesión.
* Cierre de sesión.
* Validación de sesión mediante token.

## Conductores

* Crear conductor.
* Editar conductor.
* Buscar por documento.
* Buscar por licencia.
* Buscar por estado.
* Cambiar estado.

## Vehículos

* Crear vehículo.
* Editar vehículo.
* Buscar por placa.
* Buscar por tipo.
* Buscar por estado.
* Cambiar estado.

## Rutas

* Crear rutas.
* Editar rutas.
* Buscar por ciudad.
* Consultar tiempo estimado.

## Programación de viajes

* Programar viajes.
* Consultar programación.
* Buscar por conductor.
* Buscar por vehículo.
* Buscar por fecha.
* Buscar por estado.

## Seguimiento de viajes

* Iniciar viaje.
* Registrar novedades.
* Finalizar viaje.
* Consultar historial de seguimiento.

---

# Validaciones implementadas

* No permitir documentos duplicados.
* No permitir licencias duplicadas.
* No permitir placas duplicadas.
* Validar capacidad mayor a cero.
* Validar distancia mayor a cero.
* No permitir conductores inactivos.
* No permitir vehículos en mantenimiento.
* Validar disponibilidad de conductor.
* Validar disponibilidad de vehículo.
* No permitir iniciar viajes cancelados.
* No permitir finalizar viajes no iniciados.
* Validar existencia de programación.

---

# Autor

Edwin
