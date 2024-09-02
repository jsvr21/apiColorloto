# Usa una imagen base de PHP
FROM php:8.0-cli

# Copia los archivos del proyecto al contenedor
COPY . /app

# Establece el directorio de trabajo
WORKDIR /app

# Expone el puerto en el que el servidor escuchará
EXPOSE 80

# Comando para iniciar el servidor PHP integrado
CMD ["php", "-S", "0.0.0.0:80"]

