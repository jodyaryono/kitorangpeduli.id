<?php $composerBin = isset($composerBin) ? $composerBin : null; ?>
<?php $phpBin = isset($phpBin) ? $phpBin : null; ?>
<?php $appDir = isset($appDir) ? $appDir : null; ?>
<?php $__container->servers(['production' => 'root@103.185.52.124']); ?>

<?php
    $appDir = '/var/www/kitorangpeduli.id';
    $phpBin = '/usr/bin/php8.2';
    $composerBin = '/usr/local/bin/composer';
?>

<?php $__container->startMacro('deploy'); ?>
    deployment_start
    git_pull
    install_dependencies
    build_assets
    run_migrations
    optimize_app
    restart_services
    deployment_complete
<?php $__container->endMacro(); ?>

<?php $__container->startTask('deployment_start'); ?>
    echo "🚀 Starting deployment of KitOrangPeduli..."
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
<?php $__container->endTask(); ?>

<?php $__container->startTask('git_pull'); ?>
    echo "📥 Pulling latest code from repository..."
    cd <?php echo $appDir; ?>

    git pull origin main
<?php $__container->endTask(); ?>

<?php $__container->startTask('install_dependencies'); ?>
    echo "📦 Installing PHP dependencies..."
    cd <?php echo $appDir; ?>

    <?php echo $phpBin; ?> <?php echo $composerBin; ?> install --optimize-autoloader --no-dev --no-interaction

    echo "📦 Installing Node dependencies..."
    npm ci --production=false
<?php $__container->endTask(); ?>

<?php $__container->startTask('build_assets'); ?>
    echo "🔨 Building frontend assets..."
    cd <?php echo $appDir; ?>

    npm run build
<?php $__container->endTask(); ?>

<?php $__container->startTask('run_migrations'); ?>
    echo "🗄️ Running database migrations..."
    cd <?php echo $appDir; ?>

    <?php echo $phpBin; ?> artisan migrate --force --no-interaction
<?php $__container->endTask(); ?>

<?php $__container->startTask('optimize_app'); ?>
    echo "⚡ Optimizing application..."
    cd <?php echo $appDir; ?>

    <?php echo $phpBin; ?> artisan config:cache
    <?php echo $phpBin; ?> artisan route:cache
    <?php echo $phpBin; ?> artisan view:cache
    <?php echo $phpBin; ?> artisan event:cache
    <?php echo $phpBin; ?> artisan filament:optimize
<?php $__container->endTask(); ?>

<?php $__container->startTask('restart_services'); ?>
    echo "🔄 Restarting services..."
    supervisorctl restart kitorangpeduli-worker:*
    echo "✓ Queue workers restarted"
<?php $__container->endTask(); ?>

<?php $__container->startTask('deployment_complete'); ?>
    echo ""
    echo "✅ Deployment completed successfully!"
    echo "🌐 Application is live at https://kitorangpeduli.id"
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
<?php $__container->endTask(); ?>

<?php $__container->startMacro('rollback'); ?>
    rollback_start
    git_rollback
    install_dependencies
    build_assets
    optimize_app
    restart_services
    rollback_complete
<?php $__container->endMacro(); ?>

<?php $__container->startTask('rollback_start'); ?>
    echo "⏪ Starting rollback..."
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
<?php $__container->endTask(); ?>

<?php $__container->startTask('git_rollback'); ?>
    echo "⏪ Rolling back to previous commit..."
    cd <?php echo $appDir; ?>

    git reset --hard HEAD~1
<?php $__container->endTask(); ?>

<?php $__container->startTask('rollback_complete'); ?>
    echo ""
    echo "✅ Rollback completed successfully!"
    echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
<?php $__container->endTask(); ?>

<?php $_vars = get_defined_vars(); $__container->finished(function($exitCode = null) use ($_vars) { extract($_vars); 
    echo "🎉 Deployment script finished!"
}); ?>
