# Capital Humano ITECH - Proyecto MVC PHP

Proyecto PHP con estructura MVC para registrar inscriptores de un evento tecnológico.

## Incluye

- Formulario con Identidad, nombre, apellido, edad, sexo, país de residencia, nacionalidad, correo, celular, temas tecnológicos y observaciones.
- Base de datos `parcial_itech` compatible con phpMyAdmin / MariaDB.
- Restricciones de integridad referencial con `ON UPDATE CASCADE` y `ON DELETE RESTRICT` donde corresponde.
- Usuario de base de datos `itech_app` con permisos mínimos para la aplicación.
- Validación y sanitización centralizadas.
- Firma digital con OpenSSL.
- Reporte web.
- Exportación a Excel compatible y soporte para PhpSpreadsheet si se instala con Composer.

## Instalación

1. Copia la carpeta del proyecto dentro de `htdocs`.
2. Importa en phpMyAdmin el archivo:

```txt
database/parcial_itech.sql
```

3. Verifica que MariaDB esté activo.
4. Abre:

```txt
http://localhost/capital_humano_itech_funcional/public/
```

## Usuario de base de datos

El SQL crea este usuario:

```txt
Usuario: itech_app
Clave: ItechApp2026*
Base: parcial_itech
```

La conexión está configurada en:

```txt
app/Config/Database.php
```

## Excel con Composer

Si Composer funciona en tu equipo, puedes instalar PhpSpreadsheet así:

```bash
composer require phpoffice/phpspreadsheet
```

Si no lo instalas, el proyecto usa una exportación `.xls` compatible con Excel.
