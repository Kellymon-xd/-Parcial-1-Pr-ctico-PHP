# Proyecto PHP MVC - Registro ITECH

Proyecto completo en PHP MVC para registrar inscriptores del evento ITECH usando MariaDB/phpMyAdmin. Está ajustado a la base `parcial_itech` enviada por la profesora.

## Incluye

- MVC puro en PHP.
- PDO con consultas preparadas.
- Sanitización y validación separadas en `app/Utils`.
- Base de datos con claves foráneas, `ON UPDATE CASCADE` y `ON DELETE RESTRICT` donde corresponde.
- Tabla intermedia `inscriptor_temas` para la relación muchos a muchos.
- Usuario de base de datos `itech_app` con permisos mínimos.
- Reporte con verificación de integridad por OpenSSL.
- Exportación a Excel con PhpSpreadsheet si está instalado, y respaldo `.xls` si Composer no está disponible.

## Instalación rápida

1. Copia la carpeta del proyecto dentro de `htdocs`.
2. Importa en phpMyAdmin el archivo:

   `database/parcial_itech.sql`

3. Abre:

   `http://localhost/capital_humano_itech_funcional/public/`

## Conexión de base de datos

El archivo `app/Config/Database.php` está configurado así:

```php
private const HOST = 'localhost';
private const DB_NAME = 'parcial_itech';
private const USER = 'itech_app';
private const PASSWORD = 'ItechApp2026*';
```

Ese usuario se crea al importar el SQL.

## Composer / Excel

Para usar Excel real `.xlsx`, ejecuta desde la raíz del proyecto:

```bash
composer require phpoffice/phpspreadsheet
```

Si Composer no está instalado o falla, el botón de Excel sigue funcionando con exportación compatible `.xls`.

## Estructura usada por la base

- `paises(id, nombre)`
- `areas_interes(id, nombre)`
- `inscriptores(id, nombre, apellido, edad, sexo, pais_residencia_id, nacionalidad_id, correo, celular, observaciones, fecha_registro)`
- `inscriptor_temas(id, inscriptor_id, area_interes_id)`
- `firmas_digitales(id, inscriptor_id, firma_digital, algoritmo, fecha_firma)`

## Nota sobre OpenSSL

En XAMPP se busca automáticamente:

`C:/xampp/apache/conf/openssl.cnf`

Si en tu instalación está en otra ruta, revisa `app/Services/FirmaDigitalService.php`.
