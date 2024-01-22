# Use the official PHP 8.2 image as the base image
FROM php:8.2

# Set the working directory
WORKDIR /var/www/html

# Copy your application code into the container
COPY . .

# Install cURL extension
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install -j$(nproc) curl

# Expose the port your application will run on
EXPOSE 80

# Start PHP server
CMD ["php", "-S", "0.0.0.0:80"]