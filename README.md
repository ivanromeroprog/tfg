# Edu-track: Instructivo de instalación y utilización

## Utilización de la aplicación

Puede acceder a un demo online de la aplicación en la URL:

---
### Importante: 
_La funcionalidad de actualización en tiempo real del estado de las actividades se encuentra offline, ya que el servicio usado fue dado de baja, puede generar algunos errores._

---

[https://edutrack.ivanyromero.com.ar/](https://edutrack.ivanyromero.com.ar/)

Los datos de acceso para un usuario docente, con datos de prueba ya cargados, son los siguientes:

- Usuario: roberto
- Clave: roberto#5

Puede también, registrar un nuevo usuario docente, para luego crear los cursos y actividades necesarias para realizar pruebas del sistema.

## Cursos y alumnos

Para poder operar como docente, primero debe crear al menos un curso. Una vez creado, podrá agregar alumnos al curso.

## Asistencia

En esta sección podrá tomar asistencia en el curso seleccionado. Copiando el link generado y, usándolo en el mismo u otro dispositivo, podrá ingresar como un alumno de dicho curso. El código único de cada alumno se puede visualizar desde la vista del docente posicionando el cursor del mouse sobre el nombre del alumno. De todas formas, se deja a continuación los datos de acceso de un alumno ya cargado en el sistema:

- Alumno: Ayala, Juan
- CUA: mt0001

Así podrá comprobar como la interacción de los alumnos es inmediatamente visualizada por el docente.

## Actividad y presentación de actividad

Para presentar una actividad primero debe crear una actividad (o utilizar las que se encuentran cargadas para el usuario de prueba). Luego podrá presentar una actividad, proceso que es muy similar a tomar asistencias, pero donde podrá visualizar si los alumnos están contestando correctamente cada punto de la actividad.

## Reporte

Finalmente, podrá visualizar un reporte de las actividades realizadas por los alumnos.

## Instalación en servidor local

### Requerimientos

Para la ejecución en local de este proyecto se debe utilizar:

- Servidor de Base de Datos: MySQL 8 o MariaDB 10
- Servidor Web: Apache 2.4 con PHP 8.1.8 instalado (no compatible con versiones anteriores a 8.1).
- Servidor Mercure HUB (disponible en [https://mercure.rocks/docs/hub/install](https://mercure.rocks/docs/hub/install))

### Ejecución del Mercure HUB

Para lograr la funcionalidad de actualización en tiempo real de datos, tanto de actividades como de asistencia, se debe correr una instancia de un servidor Mercure HUB. El resto de la aplicación funciona correctamente sin este. Se adjunta el archivo de configuración del servidor Mercure HUB (“Caddyfile.dev”) en la carpeta “tfg”. Se debe ejecutar el servidor con la siguiente línea de comando desde PowerShell en Windows.

$env:MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!';
$env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!';
.\mercure.exe run -config Caddyfile.dev

### Instalación
- Descargar el proyecto con sus dependencias desde el siguiente link: https://drive.google.com/file/d/1rmEkND-t9EBURZEAtdI03bn7cCHE_BiJ/view?usp=drive_link
- Copiar el contenido de la carpeta “tfg” a la raíz del servidor web apache.
- Importar la base de datos desde el archivo “BASE_DE_DATOS_tfg.sql” al servidor MySQL/MariaDB.
- Configurar la versión del servidor y datos de acceso a la base de datos en el archivo “.env.local”.
- Acceder a localhost desde un navegador web.
