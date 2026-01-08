# Deployment Guide

This guide covers deploying the Jangan Lupa Untung Laravel application using Docker and Railway.app.

## Table of Contents

- [Docker Deployment](#docker-deployment)
- [Railway.app Deployment](#railwayapp-deployment)
- [Environment Configuration](#environment-configuration)
- [Troubleshooting](#troubleshooting)

## Docker Deployment

### Prerequisites

- Docker 20.10 or higher
- Docker Compose 2.0 or higher

### Local Development with Docker Compose

1. **Clone the repository:**
   ```bash
   git clone https://github.com/robyoreal/janganlupauntung.git
   cd janganlupauntung
   ```

2. **Create environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Generate application key:**
   ```bash
   # Generate a random 32-character key
   php artisan key:generate
   # Or manually set in .env: APP_KEY=base64:YOUR_32_CHARACTER_KEY
   ```

4. **Build and start containers:**
   ```bash
   docker-compose up -d --build
   ```

5. **Access the application:**
   - Application: http://localhost:8000
   - Database: localhost:3306 (credentials in docker-compose.yml)

6. **View logs:**
   ```bash
   docker-compose logs -f app
   ```

7. **Stop containers:**
   ```bash
   docker-compose down
   ```

### Production Docker Build

Build the production Docker image:

```bash
docker build -t janganlupauntung:latest .
```

Run the production container:

```bash
docker run -d \
  -p 80:80 \
  -e APP_KEY=your_app_key \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=your_db_host \
  -e DB_DATABASE=your_database \
  -e DB_USERNAME=your_username \
  -e DB_PASSWORD=your_password \
  --name janganlupauntung \
  janganlupauntung:latest
```

## Railway.app Deployment

Railway.app is a modern platform for deploying applications with minimal configuration.

### Prerequisites

- GitHub account
- Railway.app account (sign up at https://railway.app)
- Repository pushed to GitHub

### Step-by-Step Deployment

#### 1. Create New Project in Railway

1. Go to https://railway.app/dashboard
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Authorize Railway to access your GitHub account
5. Select the `robyoreal/janganlupauntung` repository

#### 2. Add MySQL Database

1. In your Railway project, click **"New"**
2. Select **"Database"**
3. Choose **"MySQL"**
4. Railway will automatically create and configure the database

#### 3. Configure Environment Variables

Railway automatically injects database credentials, but you need to set additional variables:

1. Click on your application service
2. Go to **"Variables"** tab
3. Add the following variables:

**Required Variables:**
```
APP_NAME=Jangan Lupa Untung
APP_ENV=production
APP_KEY=<generate-with-php-artisan-key-generate>
APP_DEBUG=false
APP_URL=https://your-app.railway.app
```

**Database Variables (Auto-configured by Railway):**
```
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}
```

**Session & Cache:**
```
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

**Important:** Use Railway's variable references (e.g., `${{MYSQLHOST}}`) for database credentials. Railway will automatically replace these with actual values.

#### 4. Generate Application Key

Railway doesn't have a built-in way to run `php artisan key:generate`, so you need to generate it locally:

```bash
# On your local machine (requires PHP)
php artisan key:generate --show

# Or use an online base64 encoder to create a 32-character random string
# Then format it as: base64:YOUR_BASE64_ENCODED_32_CHAR_STRING
```

Add the generated key to Railway's `APP_KEY` variable.

#### 5. Deploy

1. Railway automatically deploys when you push to your GitHub repository
2. Or click **"Deploy"** in the Railway dashboard
3. Monitor the build logs in the **"Deployments"** tab

#### 6. Set Up Custom Domain (Optional)

1. In your service settings, go to **"Settings"**
2. Click **"Generate Domain"** for a Railway subdomain
3. Or add a custom domain in the **"Domains"** section

#### 7. Verify Deployment

1. Once deployed, visit your Railway domain
2. Check the health endpoint: `https://your-app.railway.app/up`
3. You should see the application running

### Railway Configuration Files

The repository includes:

- **`railway.toml`**: Railway build and deployment configuration
- **`.env.production.example`**: Example environment variables for production
- **`Dockerfile`**: Multi-stage build configuration
- **`docker/docker-entrypoint.sh`**: Startup script for migrations and optimizations

### Automatic Migrations

The `docker-entrypoint.sh` script automatically:

1. Waits for database connection
2. Runs database migrations (`php artisan migrate --force`)
3. Caches routes, config, and views
4. Optimizes the application

## Environment Configuration

### Required Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_KEY` | Application encryption key | `base64:random32chars...` |
| `APP_URL` | Your application URL | `https://app.railway.app` |
| `DB_CONNECTION` | Database driver | `mysql` or `pgsql` |
| `DB_HOST` | Database host | `${{MYSQLHOST}}` |
| `DB_DATABASE` | Database name | `${{MYSQLDATABASE}}` |
| `DB_USERNAME` | Database user | `${{MYSQLUSER}}` |
| `DB_PASSWORD` | Database password | `${{MYSQLPASSWORD}}` |

### Optional Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `QUEUE_CONNECTION` | Queue driver | `database` |
| `CACHE_STORE` | Cache driver | `database` |
| `SESSION_DRIVER` | Session driver | `database` |
| `LOG_LEVEL` | Logging level | `error` |
| `MAIL_MAILER` | Mail driver | `log` |

### Using PostgreSQL Instead of MySQL

If you prefer PostgreSQL:

1. Add PostgreSQL database in Railway instead of MySQL
2. Update environment variables:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=${{PGHOST}}
   DB_PORT=${{PGPORT}}
   DB_DATABASE=${{PGDATABASE}}
   DB_USERNAME=${{PGUSER}}
   DB_PASSWORD=${{PGPASSWORD}}
   ```

## Troubleshooting

### Build Failures

**Issue:** Docker build fails with "npm install" errors

**Solution:** 
- Check that `package.json` and `package-lock.json` are committed
- Verify Node.js version in Dockerfile matches your development environment

**Issue:** Composer install fails

**Solution:**
- Ensure `composer.lock` is committed to the repository
- Check PHP version compatibility in `composer.json`

### Runtime Issues

**Issue:** 500 Internal Server Error

**Solutions:**
1. Check Railway logs: Click on deployment → View Logs
2. Verify `APP_KEY` is set correctly
3. Ensure database credentials are correct
4. Check storage permissions (handled by entrypoint script)

**Issue:** Database connection refused

**Solutions:**
1. Verify Railway database service is running
2. Check database variable references: `${{MYSQLHOST}}` not `${MYSQLHOST}`
3. Ensure application and database are in the same Railway project

**Issue:** Assets not loading (404 on CSS/JS)

**Solutions:**
1. Verify Vite build completed successfully in build logs
2. Check that `public/build` directory exists in the image
3. Ensure `APP_URL` is set correctly

### Debugging in Railway

**View Application Logs:**
```
1. Go to your Railway project
2. Click on your service
3. Go to "Deployments" tab
4. Click on the active deployment
5. View "Logs" tab
```

**Access Build Logs:**
```
1. Go to "Deployments" tab
2. Click on a deployment
3. View "Build Logs" tab
```

**Check Health Status:**
Visit `https://your-app.railway.app/up` - should return HTTP 200

### Common Railway Issues

**Issue:** "Deployment failed - Health check timeout"

**Solution:**
- Increase `healthcheckTimeout` in `railway.toml`
- Check that migrations aren't taking too long
- Verify the `/up` endpoint is accessible

**Issue:** Queue workers not processing jobs

**Solution:**
- Check supervisor logs in application logs
- Verify `QUEUE_CONNECTION=database` is set
- Ensure `jobs` table exists (created by migrations)

### Database Issues

**Manually run migrations:**
```bash
# Get Railway CLI: https://docs.railway.app/develop/cli
railway run php artisan migrate --force
```

**Reset database (⚠️ destroys all data):**
```bash
railway run php artisan migrate:fresh --force
```

**Seed database:**
```bash
railway run php artisan db:seed --force
```

## Performance Optimization

### Production Checklist

- [x] `APP_DEBUG=false` (security & performance)
- [x] `APP_ENV=production`
- [x] OPcache enabled (configured in `docker/php.ini`)
- [x] Config, routes, and views cached (automatic in entrypoint)
- [x] Composer autoloader optimized (built into Dockerfile)
- [x] Vite assets compiled (built into Dockerfile)
- [x] Gzip compression enabled (nginx configuration)

### Scaling on Railway

Railway automatically scales based on your plan:

1. **Vertical Scaling**: Railway automatically allocates resources
2. **Horizontal Scaling**: Available on Team and Enterprise plans
3. **Database Scaling**: Can be configured in database settings

### Monitoring

**Railway Built-in Monitoring:**
- CPU usage
- Memory usage
- Network traffic
- Request metrics

**Application Monitoring:**
- Laravel logs: `storage/logs/laravel.log`
- Queue worker logs: `storage/logs/queue-worker.log`
- Nginx access/error logs

## Support

### Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Railway Documentation**: https://docs.railway.app
- **Docker Documentation**: https://docs.docker.com
- **Repository Issues**: https://github.com/robyoreal/janganlupauntung/issues

### Getting Help

1. Check the troubleshooting section above
2. Review Railway deployment logs
3. Check Laravel logs in storage/logs
4. Open an issue on GitHub with:
   - Error messages
   - Railway logs
   - Environment configuration (redact sensitive data)

## Updates and Maintenance

### Deploying Updates

Railway automatically deploys when you push to your connected GitHub branch:

```bash
git add .
git commit -m "Your update message"
git push origin main
```

### Manual Deployment Trigger

1. Go to Railway dashboard
2. Click on your service
3. Go to "Deployments" tab
4. Click "Redeploy" on the latest deployment

### Rolling Back

1. Go to "Deployments" tab
2. Find a previous successful deployment
3. Click "..." menu → "Redeploy"

## Security Considerations

1. **Never commit** `.env` file
2. **Always set** `APP_DEBUG=false` in production
3. **Use strong** `APP_KEY` (32 random characters)
4. **Keep dependencies updated**: `composer update` and `npm update`
5. **Use HTTPS**: Railway provides SSL/TLS automatically
6. **Secure database credentials**: Use Railway's variable references
7. **Review logs regularly** for suspicious activity

## License

This deployment configuration is part of the Jangan Lupa Untung project and is available under the MIT License.
