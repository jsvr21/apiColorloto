# Usa una imagen base de PHP
FROM php:8.0-cli

# Copia el código fuente al contenedor
COPY public/ /var/www/html/

# Expone el puerto en el que el servidor PHP escuchará
EXPOSE 80

# Configura el comando para iniciar el servidor PHP
CMD ["php", "-S", "0.0.0.0:80", "index.php"]

