@servers(['production' => 'root@103.185.52.124'])

@setup
    $appDir = '/var/www/kitorangpeduli.id';
    $phpBin = '/usr/bin/php8.2';
    $composerBin = '/usr/local/bin/composer';
@endsetup

@story('deploy')
    deployment_start
    git_pull
    install_dependencies
    build_assets
    run_migrations
    optimize_app
    restart_services
    deployment_complete
@endstory

@task('deployment_start')
    echo "🚀 Starting deployment of KitOrangPeduli..."
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
@endtask

@task('git_pull')
    echo "📥 Pulling latest code from repository..."
    cd {{ $appDir }}
    git pull origin main
@endtask

@task('install_dependencies')
    echo "📦 Installing PHP dependencies..."
    cd {{ $appDir }}
    {{ $phpBin }} {{ $composerBin }} install --optimize-autoloader --no-dev --no-interaction

    echo "📦 Installing Node dependencies..."
    npm ci --production=false
@endtask

@task('build_assets')
    echo "🔨 Building frontend assets..."
    cd {{ $appDir }}
    npm run build
@endtask

@task('run_migrations')
    echo "🗄️ Running database migrations..."
    cd {{ $appDir }}
    {{ $phpBin }} artisan migrate --force --no-interaction
@endtask

@task('optimize_app')
    echo "⚡ Optimizing application..."
    cd {{ $appDir }}
    {{ $phpBin }} artisan config:cache
    {{ $phpBin }} artisan route:cache
    {{ $phpBin }} artisan view:cache
    {{ $phpBin }} artisan event:cache
    {{ $phpBin }} artisan filament:optimize
@endtask

@task('restart_services')
    echo "🔄 Restarting services..."
    supervisorctl restart kitorangpeduli-worker:*
    echo "✓ Queue workers restarted"
@endtask

@task('deployment_complete')
    echo ""
    echo "✅ Deployment completed successfully!"
    echo "🌐 Application is live at https://kitorangpeduli.id"
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
@endtask

@story('rollback')
    rollback_start
    git_rollback
    install_dependencies
    build_assets
    optimize_app
    restart_services
    rollback_complete
@endstory

@task('rollback_start')
    echo "⏪ Starting rollback..."
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
@endtask

@task('git_rollback')
    echo "⏪ Rolling back to previous commit..."
    cd {{ $appDir }}
    git reset --hard HEAD~1
@endtask

@task('rollback_complete')
    echo ""
    echo "✅ Rollback completed successfully!"
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
@endtask

@finished
    echo "🎉 Deployment script finished!"
@endfinished

@error
    echo "❌ Deployment failed!"
    echo "Check the logs for more details."
@enderror
