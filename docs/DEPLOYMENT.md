# 🚀 GUÍA DE DEPLOYMENT
## Manual Completo para el Primer Despliegue en Servidor

---

## 🎯 PROPÓSITO

Este documento detalla **TODO** lo que necesitas configurar en el servidor para hacer el primer deployment del proyecto. Incluye requisitos, configuración step-by-step, troubleshooting y checklist de validación.

---

## 📋 REQUISITOS DEL SERVIDOR

### Software Obligatorio

| Software | Versión Mínima | Recomendada | Notas |
|----------|---------------|-------------|-------|
| **PHP** | 8.2 | 8.3+ | Con extensiones requeridas |
| **Composer** | 2.5 | Latest | Gestor de dependencias PHP |
| **Node.js** | 18 | 20 LTS | Para compilar assets |
| **NPM** | 9 | Latest | Viene con Node.js |
| **MySQL** | 8.0 | 8.0+ | O MariaDB 10.5+ |
| **Nginx** | 1.20+ | Latest | O Apache 2.4+ |
| **Git** | 2.30+ | Latest | Control de versiones |
| **Redis** | 6.0+ | Latest | Opcional (cache, queues) |

---

### Extensiones PHP Requeridas

Verificar con: `php -m`

```bash
✅ BCMath
✅ Ctype
✅ cURL
✅ DOM
✅ Fileinfo
✅ JSON
✅ Mbstring
✅ OpenSSL
✅ PCRE
✅ PDO
✅ PDO_MySQL
✅ Tokenizer
✅ XML
✅ GD o Imagick      # Para procesamiento de imágenes
✅ Zip               # Para manipulación de archivos
```

**Instalar extensiones faltantes (Ubuntu/Debian)**:
```bash
sudo apt-get update
sudo apt-get install -y \
    php8.3-bcmath \
    php8.3-curl \
    php8.3-gd \
    php8.3-mbstring \
    php8.3-mysql \
    php8.3-xml \
    php8.3-zip \
    php8.3-imagick
```

---

## 🔧 CONFIGURACIÓN INICIAL DEL SERVIDOR

### 1. Preparar el Entorno

```bash
# Conectar al servidor
ssh user@your-server.com

# Actualizar sistema
sudo apt-get update && sudo apt-get upgrade -y

# Instalar utilidades básicas
sudo apt-get install -y git unzip curl wget software-properties-common
```

---

### 2. Instalar PHP 8.3

```bash
# Añadir repositorio PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update

# Instalar PHP y extensiones
sudo apt-get install -y php8.3-fpm php8.3-cli php8.3-common

# Instalar extensiones necesarias
sudo apt-get install -y \
    php8.3-mysql \
    php8.3-xml \
    php8.3-mbstring \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-bcmath \
    php8.3-imagick \
    php8.3-redis

# Verificar instalación
php -v
php -m | grep -E 'pdo_mysql|gd|mbstring|xml'
```

**Configurar PHP** (`/etc/php/8.3/fpm/php.ini`):
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
max_input_time = 300
```

---

### 3. Instalar Composer

```bash
# Descargar installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Verificar installer
php -r "if (hash_file('sha384', 'composer-setup.php') === file_get_contents('https://composer.github.io/installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

# Instalar globalmente
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Limpiar
php -r "unlink('composer-setup.php');"

# Verificar
composer --version
```

---

### 4. Instalar Node.js y NPM

```bash
# Instalar Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verificar
node -v
npm -v
```

---

### 5. Instalar MySQL

```bash
# Instalar MySQL Server
sudo apt-get install -y mysql-server

# Securizar instalación
sudo mysql_secure_installation

# Crear base de datos y usuario
sudo mysql -u root -p
```

```sql
CREATE DATABASE reviews CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'reviews_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON reviews.* TO 'reviews_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

### 6. Instalar Nginx

```bash
# Instalar Nginx
sudo apt-get install -y nginx

# Iniciar y habilitar
sudo systemctl start nginx
sudo systemctl enable nginx

# Verificar
sudo systemctl status nginx
```

---

### 7. Instalar Redis (Opcional pero Recomendado)

```bash
# Instalar Redis
sudo apt-get install -y redis-server

# Configurar para iniciar automáticamente
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Verificar
redis-cli ping
# Debe responder: PONG
```

---

## 📦 DEPLOYMENT DE LA APLICACIÓN

### 1. Clonar el Repositorio

```bash
# Crear directorio de aplicaciones
sudo mkdir -p /var/www
cd /var/www

# Clonar repositorio
sudo git clone https://github.com/pitiflautico/reviews.git
cd reviews

# Dar permisos al usuario www-data
sudo chown -R www-data:www-data /var/www/reviews
sudo chmod -R 755 /var/www/reviews
```

---

### 2. Instalar Dependencias PHP

```bash
cd /var/www/reviews

# Instalar dependencias de producción
composer install --optimize-autoloader --no-dev

# Si hay problemas de permisos
sudo -u www-data composer install --optimize-autoload --no-dev
```

---

### 3. Configurar Variables de Entorno

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar configuración
nano .env
```

**Variables CRÍTICAS a configurar**:

```env
# APLICACIÓN
APP_NAME="Gastro Reviews"
APP_ENV=production
APP_KEY=                    # Se genera después
APP_DEBUG=false             # IMPORTANTE: false en producción
APP_URL=https://tudominio.com
APP_SKIN=listox

# BASE DE DATOS
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reviews
DB_USERNAME=reviews_user
DB_PASSWORD=TU_PASSWORD_AQUI

# CACHE Y SESIONES
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# MAIL (ejemplo con Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tucorreo@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# OPCIONAL: Google Maps
GOOGLE_MAPS_KEY=tu_api_key_aqui

# FILESYSTEM
FILESYSTEM_DISK=public
```

---

### 4. Generar Application Key

```bash
php artisan key:generate
```

Esto actualiza automáticamente `APP_KEY` en `.env`

---

### 5. Instalar y Compilar Assets Frontend

```bash
# Instalar dependencias NPM
npm install

# Compilar para producción
npm run build

# Verificar que se generó /public/build/
ls -la public/build/
```

---

### 6. Ejecutar Migraciones

```bash
# Ejecutar migraciones
php artisan migrate --force

# Si hay seeders de datos iniciales
php artisan db:seed --force
```

---

### 7. Crear Symlink de Storage

```bash
php artisan storage:link
```

Esto crea un enlace simbólico de `storage/app/public` a `public/storage`

---

### 8. Optimizar para Producción

```bash
# Cachear configuración
php artisan config:cache

# Cachear rutas
php artisan route:cache

# Cachear vistas
php artisan view:cache

# Optimizar autoloader de Composer
composer dump-autoload --optimize
```

---

### 9. Configurar Permisos

```bash
# Dar permisos correctos
sudo chown -R www-data:www-data /var/www/reviews

# Storage y bootstrap/cache necesitan escritura
sudo chmod -R 775 /var/www/reviews/storage
sudo chmod -R 775 /var/www/reviews/bootstrap/cache

# Verificar
ls -la storage/
ls -la bootstrap/cache/
```

---

## 🌐 CONFIGURACIÓN DE NGINX

### Crear Configuración de Sitio

```bash
sudo nano /etc/nginx/sites-available/reviews
```

**Contenido del archivo**:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name tudominio.com www.tudominio.com;

    # Redirigir a HTTPS
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name tudominio.com www.tudominio.com;

    root /var/www/reviews/public;
    index index.php;

    # SSL Certificates (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/tudominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/tudominio.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Logs
    access_log /var/log/nginx/reviews_access.log;
    error_log /var/log/nginx/reviews_error.log;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';" always;

    # Max upload size
    client_max_body_size 20M;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

---

### Activar Sitio

```bash
# Crear symlink
sudo ln -s /etc/nginx/sites-available/reviews /etc/nginx/sites-enabled/

# Eliminar sitio por defecto (opcional)
sudo rm /etc/nginx/sites-enabled/default

# Verificar configuración
sudo nginx -t

# Recargar Nginx
sudo systemctl reload nginx
```

---

## 🔒 CONFIGURAR SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt-get install -y certbot python3-certbot-nginx

# Obtener certificado
sudo certbot --nginx -d tudominio.com -d www.tudominio.com

# Verificar renovación automática
sudo certbot renew --dry-run

# Renovación se hace automáticamente vía cron
```

---

## ⚙️ CONFIGURAR CRON JOBS

Laravel necesita ejecutar el scheduler cada minuto:

```bash
# Editar crontab del usuario www-data
sudo crontab -e -u www-data
```

Añadir esta línea:

```cron
* * * * * cd /var/www/reviews && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔄 CONFIGURAR QUEUE WORKERS (Si se usan colas)

### Crear Service de Systemd

```bash
sudo nano /etc/systemd/system/reviews-worker.service
```

**Contenido**:

```ini
[Unit]
Description=Reviews Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/reviews/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

**Activar y arrancar**:

```bash
sudo systemctl daemon-reload
sudo systemctl enable reviews-worker
sudo systemctl start reviews-worker

# Verificar estado
sudo systemctl status reviews-worker
```

---

## 🔥 CONFIGURAR FIREWALL

```bash
# Instalar UFW si no está
sudo apt-get install -y ufw

# Permitir SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Activar firewall
sudo ufw enable

# Verificar
sudo ufw status
```

---

## 📊 MONITOREO Y LOGS

### Logs de Laravel

```bash
# Ver logs en tiempo real
tail -f /var/www/reviews/storage/logs/laravel.log

# Logs de Nginx
tail -f /var/log/nginx/reviews_error.log
tail -f /var/log/nginx/reviews_access.log
```

---

### Configurar Logrotate

```bash
sudo nano /etc/logrotate.d/reviews
```

```
/var/www/reviews/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

---

## ✅ CHECKLIST FINAL DE DEPLOYMENT

### Pre-Deployment

- [ ] Servidor con SO actualizado
- [ ] PHP 8.3+ instalado con todas las extensiones
- [ ] Composer instalado globalmente
- [ ] Node.js 20+ y NPM instalados
- [ ] MySQL instalado y BD creada
- [ ] Nginx instalado y configurado
- [ ] Redis instalado (opcional)
- [ ] Usuario y permisos configurados
- [ ] Firewall configurado

### Deployment

- [ ] Repositorio clonado en `/var/www/reviews`
- [ ] `composer install --no-dev` ejecutado
- [ ] `npm install && npm run build` ejecutado
- [ ] `.env` configurado correctamente
- [ ] `APP_KEY` generada
- [ ] Migraciones ejecutadas
- [ ] Storage link creado
- [ ] Cachés optimizados (config, route, view)
- [ ] Permisos de storage y cache configurados
- [ ] Nginx configurado y activo
- [ ] SSL configurado con Let's Encrypt
- [ ] Cron jobs configurados
- [ ] Queue workers configurados (si aplica)

### Post-Deployment

- [ ] Sitio accesible vía HTTPS
- [ ] Registro de usuario funciona
- [ ] Login funciona
- [ ] Subida de imágenes funciona
- [ ] Envío de emails funciona
- [ ] Logs sin errores críticos
- [ ] Rendimiento aceptable (< 300ms)
- [ ] Backup configurado

---

## 🔄 PROCESO DE ACTUALIZACIÓN

Cuando necesites deployear cambios:

```bash
# Conectar al servidor
ssh user@server.com
cd /var/www/reviews

# Poner en modo mantenimiento
php artisan down

# Obtener últimos cambios
git pull origin main

# Actualizar dependencias
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y rebuild cachés
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar workers (si aplica)
sudo systemctl restart reviews-worker

# Salir de modo mantenimiento
php artisan up
```

---

## 🐛 TROUBLESHOOTING

### Error: "500 Internal Server Error"

```bash
# Verificar permisos
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Ver logs
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/reviews_error.log
```

---

### Error: "No application encryption key"

```bash
php artisan key:generate
```

---

### Error: Subida de archivos falla

Verificar `php.ini`:
```ini
upload_max_filesize = 20M
post_max_size = 20M
```

Verificar Nginx config:
```nginx
client_max_body_size 20M;
```

Reiniciar servicios:
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

---

### Error: "SQLSTATE[HY000] [2002] Connection refused"

Verificar MySQL está corriendo:
```bash
sudo systemctl status mysql
sudo systemctl start mysql
```

Verificar credenciales en `.env`

---

### Assets no se cargan (404)

```bash
# Recompilar assets
npm run build

# Verificar permisos
ls -la public/build/

# Limpiar caché
php artisan optimize:clear
```

---

## 🔐 SEGURIDAD ADICIONAL

### Fail2Ban (protección contra brute force)

```bash
sudo apt-get install -y fail2ban

# Configurar
sudo nano /etc/fail2ban/jail.local
```

```ini
[nginx-http-auth]
enabled = true
filter = nginx-http-auth
port = http,https
logpath = /var/log/nginx/reviews_error.log
```

---

### Backups Automatizados

Crear script de backup:

```bash
sudo nano /usr/local/bin/backup-reviews.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/backups/reviews"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup BD
mysqldump -u reviews_user -p'PASSWORD' reviews > "$BACKUP_DIR/db_$DATE.sql"

# Backup archivos
tar -czf "$BACKUP_DIR/storage_$DATE.tar.gz" /var/www/reviews/storage/app

# Eliminar backups antiguos (mantener 30 días)
find $BACKUP_DIR -type f -mtime +30 -delete
```

Añadir a cron:
```bash
sudo crontab -e
```

```cron
0 2 * * * /usr/local/bin/backup-reviews.sh
```

---

## 📞 SOPORTE Y AYUDA

Si encuentras problemas:

1. Revisa logs: `storage/logs/laravel.log`
2. Revisa logs de Nginx: `/var/log/nginx/`
3. Verifica permisos: `ls -la storage/ bootstrap/cache/`
4. Consulta documentación oficial de Laravel
5. Revisa `/docs/` del proyecto

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0

---

## ⏭️ PRÓXIMO PASO

Una vez completado el deployment, consulta:
- `/docs/AI_HANDOFF.md` - Para entender el proyecto
- `/docs/PROJECT_STRUCTURE.md` - Para ubicarte en el código
- `/docs/DEVELOPMENT_GUIDE.md` - Para desarrollo futuro
