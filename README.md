# estudioBoxBackEnd
Aplicación Symfony versión 4.4 que permitirá la gestión del sistema de encuesta Estudio Box.
## Configuración
Una ves clonado el proyecto desde la ruta: https://github.com/ReynaldoBP/estudioBoxBackEnd.git
Ejecutar el siguiente comando:
#### composer update
en caso de error por la versión del composer ejecutar:
#### composer --version
#### sudo apt-get remove composer
#### composer --version
#### sudo curl -s https://getcomposer.org/installer | php
#### sudo mv composer.phar /usr/local/bin/composer
levantar la api:
#### php bin/console server:run
limpiar cache:
#### php bin/console cache:clear
crear una entidad:
#### php bin/console make:entity
crear controlador:
#### php bin/console make:controller
