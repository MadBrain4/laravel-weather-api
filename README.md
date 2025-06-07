# 🌤️ Laravel Weather API

API en Laravel para gestión de usuarios y consulta de datos climáticos usando WeatherAPI. Desarrollado siguiendo buenas prácticas de arquitectura, optimización, seguridad y pruebas.

---

## 🚀 Funcionalidades

- ✅ Registro y autenticación de usuarios con Laravel Sanctum  
- 🌡️ Consulta de clima actual por ciudad (temperatura, clima, humedad, viento, hora local)  
- 📜 Historial de búsquedas (últimas 10 ciudades sin repetir)  
- ⭐ Ciudades favoritas (verificación previa con WeatherAPI)  
- 🔒 Seguridad con middleware y validaciones  
- ⚡ Caché de resultados para optimización  
- 🌍 Soporte multiidioma (mensajes en `lang/es`)  
- 🧪 Pruebas unitarias y de integración  

---

## 🛠️ Instalación y Configuración

Sigue los siguientes pasos para instalar y ejecutar el proyecto localmente:

```bash
# 1. Clona el repositorio
git clone https://github.com/tu-usuario/laravel-weather-api.git
cd laravel-weather-api

# 2. Instala las dependencias
composer install

# 3. Copia el archivo de entorno
cp .env.example .env

# 4. Genera la clave de la aplicación
php artisan key:generate

# 5. Configura tu archivo .env con tus credenciales de base de datos y WeatherAPI
# (ver más abajo)

# 6. Ejecuta las migraciones y los seeders
php artisan migrate --seed

# 7. Publica los archivos de Sanctum (si es necesario)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 8. Ejecuta el servidor local
php artisan serve

# 9. Entrar a la ruta http://localhost:8000/docs para revisar la documentación de los endpoints
Ingresar http://localhost:8000/docs en el navegador

## 📘 Documentación de Endpoints

### 🔐 Autenticación

---

**POST `/api/register`**  
Registra un nuevo usuario.  
Parámetros en el cuerpo (formato JSON):  
`name`: Nombre del usuario,  
`email`: Correo electrónico único,  
`password`: Contraseña,  
`password_confirmation`: Confirmación de contraseña.  

**Ejemplo de solicitud:**  
{  
  "name": "Juan Pérez",  
  "email": "juan@example.com",  
  "password": "secret",  
  "password_confirmation": "secret"  
}  

**Respuesta exitosa:**  
{  
  "message": "Usuario registrado correctamente",  
  "token": "TOKEN_GENERADO"  
}

---

**POST `/api/login`**  
Inicia sesión y devuelve un token de acceso.  
Parámetros en el cuerpo (formato JSON):  
`email`: Correo electrónico del usuario,  
`password`: Contraseña.  

**Ejemplo de solicitud:**  
{  
  "email": "juan@example.com",  
  "password": "secret"  
}  

**Respuesta exitosa:**  
{  
  "message": "Login exitoso",  
  "token": "TOKEN_GENERADO"  
}

---

### 👤 Perfil y Cierre de Sesión

---

**GET `/api/me`**  
Devuelve la información del usuario autenticado.  
Requiere autenticación con token Bearer en el header.  

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO  

**Respuesta exitosa:**  
{  
  "id": 1,  
  "name": "Juan Pérez",  
  "email": "juan@example.com",  
  "created_at": "2025-06-01T12:34:56.000000Z",  
  "updated_at": "2025-06-01T12:34:56.000000Z"  
}

---

**POST `/api/logout`**  
Cierra la sesión del usuario autenticado (revoca el token actual).  
Requiere autenticación con token Bearer en el header.  

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO  

**Respuesta exitosa:**  
{  
  "message": "Sesión cerrada correctamente"  
}

---

### 🛡️ Gestión de Roles (requiere autenticación y permisos de superadmin)

Todas las rutas están protegidas por middleware `auth:sanctum` y `superadmin`.

---

**PATCH `/api/user/{user}/role`**  
Actualiza el rol de un usuario específico.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Parámetros de ruta:**  
- `user` (integer) — ID del usuario al que se le va a cambiar el rol

**Parámetros en el cuerpo (JSON):**
```json
{
  "role": "admin"
}
```

**Respuesta exitosa:**
```json
{
  "message": "Rol actualizado correctamente",
  "user": {
    "id": 2,
    "name": "Pedro Gómez",
    "email": "pedro@example.com",
    "roles": ["admin"]
  }
}
```

**Errores posibles:**
- 403 — Si se intenta cambiar el rol de un superadmin:
```json
{
  "message": "No se puede cambiar el rol de un superadmin"
}
```

---

**POST `/api/user/role`**  
Crea un nuevo rol.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Parámetros en el cuerpo (JSON):**
```json
{
  "name": "editor",
  "guard_name": "web" // Opcional, por defecto es "web"
}
```

**Respuesta exitosa (201 Created):**
```json
{
  "message": "Rol creado exitosamente",
  "role": {
    "id": 4,
    "name": "editor",
    "guard_name": "web"
  }
}
```

---

**PUT `/api/user/role/{role}`**  
Actualiza un rol existente.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Parámetros de ruta:**  
- `role` (integer) — ID del rol a modificar

**Parámetros en el cuerpo (JSON):**
```json
{
  "name": "editor_actualizado",
  "guard_name": "web"
}
```

**Respuesta exitosa:**
```json
{
  "message": "Rol actualizado exitosamente",
  "role": {
    "id": 4,
    "name": "editor_actualizado",
    "guard_name": "web"
  }
}
```

---

### 🌐 Configuración de Idioma del Usuario

Estas rutas permiten consultar y actualizar el idioma preferido del usuario autenticado.  
Requieren autenticación con `auth:sanctum`.

---

**GET `/api/user/language`**  
Obtiene el idioma actual del usuario autenticado.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Respuesta exitosa:**
```json
{
  "language": "es"
}
```

---

**PATCH `/api/user/language`**  
Actualiza el idioma del usuario autenticado.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Parámetros en el cuerpo (JSON):**
```json
{
  "language": "en"
}
```

**Idiomas disponibles:**  
Valores válidos configurados en `config/languages.php`, por ejemplo: `es`, `en`, `fr`.

**Respuesta exitosa:**
```json
{
  "message": "Idioma actualizado correctamente",
  "language": "en"
}
```

**Errores posibles:**
- 422 — Si se envía un idioma no soportado:
```json
{
  "message": "El idioma seleccionado no es válido.",
  "errors": {
    "language": ["El idioma seleccionado no es válido."]
  }
}
```

---

### ☀️ Rutas de Clima y Ciudades Favoritas

Todas las rutas están protegidas por `auth:sanctum` y se agrupan bajo el prefijo `/api/weather`.

---

#### 📍 GET `/api/weather?city=Santiago`

Consulta el clima actual de una ciudad.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Parámetros de consulta:**  
- `city` (string) — Nombre de la ciudad (requerido)

**Respuesta exitosa:**
```json
{
  "city": "Santiago",
  "temperature": 24,
  "condition": "Parcialmente nublado",
  "humidity": 60,
  "wind_kph": 13.5,
  "local_time": "2025-06-07 11:24"
}
```

**Errores posibles:**
- 502 — Fallo en conexión con WeatherAPI:
```json
{
  "message": "Error al obtener los datos del clima",
  "error": "City not found"
}
```

---

#### 📜 GET `/api/weather/history`

Obtiene el historial de las últimas 10 ciudades consultadas por el usuario (sin repetir).

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Respuesta exitosa:**
```json
{
  "history": [
    {
      "city": "Santiago",
      "consulted_at": "2025-06-06 16:32:45"
    },
    {
      "city": "Buenos Aires",
      "consulted_at": "2025-06-06 15:21:11"
    }
  ]
}
```

---

#### ⭐ GET `/api/weather/favorite-cities`

Obtiene las ciudades favoritas del usuario autenticado.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Respuesta exitosa:**
```json
{
  "data": [
    {
      "id": 1,
      "city": "Santiago",
      "user_id": 7,
      "created_at": "2025-06-05T12:33:21.000000Z",
      "updated_at": "2025-06-05T12:33:21.000000Z"
    }
  ]
}
```

---

#### ➕ POST `/api/weather/favorite-cities`

Agrega una ciudad a las favoritas del usuario.  
Se valida previamente si la ciudad es válida mediante WeatherAPI.

**Encabezado requerido:**  
Authorization: Bearer TOKEN_GENERADO

**Cuerpo de la solicitud:**
```json
{
  "city": "Santiago"
}
```

**Respuesta exitosa:**
```json
{
  "message": "Ciudad agregada a favoritos",
  "data": {
    "id": 2,
    "city": "Santiago",
    "user_id": 7,
    "created_at": "2025-06-07T14:02:10.000000Z",
    "updated_at": "2025-06-07T14:02:10.000000Z"
  }
}
```

**Errores posibles:**
- 422 — Si el campo `city` no está presente:
```json
{
  "message": "El campo city es obligatorio.",
  "errors": {
    "city": ["El campo city es obligatorio."]
  }
}
```
