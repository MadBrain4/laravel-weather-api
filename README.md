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
