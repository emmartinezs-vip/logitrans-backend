# Documentación de Endpoints - LogiTrans Backend

## Información General

Base URL local:

```text
http://localhost
```

Puertos utilizados:

| Microservicio  | Puerto |
| -------------- | ------ |
| ms-auth        | 8000   |
| ms-conductores | 8001   |
| ms-vehiculos   | 8002   |
| ms-rutas       | 8003   |
| ms-viajes      | 8004   |

---

# 1. ms-auth

## POST /login

### Descripción

Permite iniciar sesión en el sistema.

### URL

```text
http://localhost:8000/login
```

### Body

```json
{
  "usuario": "admin",
  "password": "123456"
}
```

### Respuesta exitosa

```json
{
  "success": true,
  "token": "abc123"
}
```

---

## POST /logout

### Descripción

Cierra la sesión del usuario.

### URL

```text
http://localhost:8000/logout
```

### Respuesta

```json
{
  "success": true,
  "mensaje": "Sesión cerrada correctamente"
}
```

---

## GET /usuarios

### Descripción

Lista todos los usuarios registrados.

### URL

```text
http://localhost:8000/usuarios
```

---

# 2. ms-conductores

## GET /conductores

Lista todos los conductores.

## POST /conductores

Crea un conductor.

## GET /conductores/{id}

Consulta un conductor por ID.

## GET /conductores/documento/{documento}

Consulta un conductor por documento.

## GET /conductores/licencia/{licencia}

Consulta un conductor por licencia.

## GET /conductores/estado/{estado}

Consulta conductores por estado.

## PATCH /conductores/{id}/estado

Actualiza el estado de un conductor.

## PUT /conductores/{id}

Actualiza toda la información de un conductor.

---

# 3. ms-vehiculos

## GET /vehiculos

Lista todos los vehículos.

## POST /vehiculos

Crea un vehículo.

## GET /vehiculos/{id}

Consulta vehículo por ID.

## GET /vehiculos/placa/{placa}

Consulta vehículo por placa.

## GET /vehiculos/tipo/{tipo}

Consulta vehículos por tipo.

## GET /vehiculos/estado/{estado}

Consulta vehículos por estado.

## PATCH /vehiculos/{id}/estado

Actualiza estado del vehículo.

## PUT /vehiculos/{id}

Actualiza información completa del vehículo.

---

# 4. ms-rutas

## Rutas

### GET /rutas

Lista todas las rutas.

### POST /rutas

Crea una ruta.

### GET /rutas/{id}

Consulta ruta por ID.

### PUT /rutas/{id}

Actualiza ruta.

### GET /rutas/ciudad/{ciudad}

Busca rutas por ciudad.

### GET /rutas/tiempo/{id}

Consulta tiempo estimado de una ruta.

---

## Programaciones

### GET /programaciones

Lista programaciones.

### POST /programaciones

Crea una programación de viaje.

### GET /programaciones/{id}

Consulta programación por ID.

### GET /programaciones/conductor/{id}

Consulta programaciones de un conductor.

### GET /programaciones/vehiculo/{id}

Consulta programaciones de un vehículo.

### GET /programaciones/estado/{estado}

Consulta programaciones por estado.

### GET /programaciones/fecha/{fecha}

Consulta programaciones por fecha.

### PUT /programaciones/{id}

Actualiza programación.

---

# 5. ms-viajes

## GET /seguimientos

Lista todos los seguimientos.

## GET /seguimientos/{id}

Consulta seguimiento por ID.

## GET /seguimientos/programacion/{programacion_id}

Consulta historial de seguimiento de una programación.

## POST /seguimientos/iniciar

Inicia un viaje.

### Body

```json
{
  "programacion_viaje_id": 1
}
```

## POST /seguimientos/novedad

Registra una novedad.

### Body

```json
{
  "programacion_viaje_id": 1,
  "estado": "retrasado",
  "novedad": "Retraso por tráfico"
}
```

## PATCH /seguimientos/finalizar

Finaliza un viaje.

### Body

```json
{
  "programacion_viaje_id": 1
}
```

---

# Validaciones implementadas

* No permitir documentos duplicados.
* No permitir licencias duplicadas.
* No permitir placas duplicadas.
* Validar capacidad positiva.
* Validar distancia positiva.
* No permitir conductores inactivos.
* No permitir vehículos en mantenimiento.
* No permitir asignaciones duplicadas.
* No permitir iniciar viajes cancelados.
* No permitir finalizar viajes no iniciados.
* Validar existencia de programación.
