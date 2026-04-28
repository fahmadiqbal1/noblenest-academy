# Docker Setup Guide — Noble Nest Academy

## Quick Start

### 1. Ensure Docker and Docker Compose are installed
```bash
docker --version  # Should be 20.10+
docker-compose --version  # Should be 2.0+
```

### 2. Set up environment
```bash
cp .env.example .env
# Edit .env with your secrets
```

### 3. Build and start containers
```bash
docker-compose up -d --build
```

### 4. Run initial setup
```bash
# Migrate database and seed
docker-compose exec app php artisan migrate --seed

# Cache optimization
docker-compose exec app php artisan optimize

# Generate application key (if not set)
docker-compose exec app php artisan key:generate
```

### 5. Verify health
```bash
curl http://localhost:8000/health
# Expected: {"status":"healthy",...} (200 OK)
```

---

## Services

| Service | Purpose | Port | Volume |
|---------|---------|------|--------|
| **app** | PHP-FPM + Nginx + Horizon | 8000 | Entire project |
| **db** | MySQL 8.0 | 3306 | db_data |
| **redis** | Cache/Session/Queue | 6379 | redis_data |
| **mailhog** | Email testing (dev) | 8025 | None |

---

## Common Commands

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f redis
```

### Run artisan commands
```bash
docker-compose exec app php artisan [command]
```

### Run migrations
```bash
docker-compose exec app php artisan migrate
```

### Run tests
```bash
docker-compose exec app php vendor/bin/phpunit
```

### Access MySQL
```bash
docker-compose exec db mysql -u noblenest -p noblenest
```

### Access Redis
```bash
docker-compose exec redis redis-cli
```

### Rebuild containers
```bash
docker-compose down
docker-compose up -d --build
```

### Stop all containers
```bash
docker-compose stop
```

### Remove all containers and volumes (CAUTION)
```bash
docker-compose down -v
```

---

## Development (with Mailhog)

```bash
docker-compose --profile dev up -d --build
```

Then visit http://localhost:8025 to see captured emails.

---

## Production Deployment

### Using Docker on Hostinger VPS or Any Hosting

1. **SSH into server and install Docker**
   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh
   sh get-docker.sh
   ```

2. **Clone repository**
   ```bash
   git clone [repo-url] /opt/noblenest
   cd /opt/noblenest
   ```

3. **Set up .env for production**
   ```bash
   cp .env.example .env
   # Edit with production secrets (use vault if available)
   ```

4. **Start containers**
   ```bash
   docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
   ```

5. **Run migrations**
   ```bash
   docker-compose exec app php artisan migrate --force
   ```

6. **Configure Nginx reverse proxy** (external to Docker)
   ```nginx
   # /etc/nginx/sites-available/noblenest
   server {
       listen 80;
       server_name noblenest.local;
       
       location / {
           proxy_pass http://127.0.0.1:8000;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
       }
   }
   ```

7. **Set up SSL** (via Certbot)
   ```bash
   sudo certbot certonly --standalone -d noblenest.local
   ```

---

## Troubleshooting

### Containers won't start
```bash
# Check logs
docker-compose logs

# Rebuild
docker-compose down -v
docker-compose up -d --build
```

### Database connection refused
```bash
# Wait for MySQL to be ready (up to 30s)
docker-compose exec app php artisan migrate
```

### Redis connection refused
```bash
# Check Redis is running
docker-compose ps redis

# Check Redis password matches .env
docker-compose exec redis redis-cli -a [REDIS_PASSWORD] ping
```

### Out of disk space
```bash
# Clean up unused Docker resources
docker system prune -a

# Remove old images
docker image prune -a
```

### Permission denied on volumes
```bash
# Fix volume permissions
docker-compose exec app chown -R www-data:www-data /app/storage
docker-compose exec app chmod -R 775 /app/storage
```

---

## Health Monitoring

Container health checks are configured via:
- **app** — `GET /health` endpoint
- **db** — `mysqladmin ping`
- **redis** — `redis-cli ping`

View health status:
```bash
docker-compose ps
```

---

## Secrets Management

### For Development
Store secrets in `.env` file (ignored by git).

### For Production
Use environment variable management:
1. **GitHub Secrets** (for CI/CD)
2. **Hostinger Control Panel** (environment variables)
3. **HashiCorp Vault** (enterprise)
4. **AWS Secrets Manager** (if on AWS)

Never commit `.env` to git.

---

## Backup and Restore

### Backup database
```bash
docker-compose exec db mysqldump -u noblenest -p noblenest > backup.sql
```

### Restore database
```bash
docker-compose exec -T db mysql -u noblenest -p noblenest < backup.sql
```

### Backup volumes
```bash
docker run --rm -v noblenest-db_data:/data -v $(pwd):/backup \
    alpine tar czf /backup/db_backup.tar.gz -C /data .
```

---

## Performance Tuning

### Increase PHP memory
Edit `.docker/php.ini`:
```ini
memory_limit = 512M  # Increased from 256M
```

### Increase MySQL connections
Edit `docker-compose.yml`:
```yaml
db:
  command: --max_connections=1000
```

### Enable query caching (MySQL)
```yaml
db:
  command: --query_cache_size=256M --query_cache_type=1
```

---

## See Also
- `Dockerfile` — Application container specification
- `docker-compose.yml` — Service definitions
- `.env.example` — Environment variables template
