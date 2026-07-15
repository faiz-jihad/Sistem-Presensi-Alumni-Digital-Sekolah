module.exports = {
  apps: [
    // ─── WhatsApp Gateway (Node.js) ──────────────────────────────
    {
      name: 'whatsapp-gateway',
      script: 'whatsapp-service.js',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '512M',
      error_file: 'storage/logs/pm2-whatsapp-error.log',
      out_file: 'storage/logs/pm2-whatsapp-out.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss',
      env: {
        NODE_ENV: 'production',
        PORT: 5000,
      },
    },

    // ─── Laravel Queue Worker ─────────────────────────────────────
    {
      name: 'queue-worker',
      script: 'php',
      args: 'artisan queue:work database --sleep=3 --tries=3 --max-time=3600',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '256M',
      error_file: 'storage/logs/pm2-queue-error.log',
      out_file: 'storage/logs/pm2-queue-out.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss',
      env: {
        APP_ENV: 'production',
      },
    },

    // ─── Laravel Scheduler ────────────────────────────────────────
    {
      name: 'scheduler',
      script: 'php',
      args: 'artisan schedule:work',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '128M',
      error_file: 'storage/logs/pm2-scheduler-error.log',
      out_file: 'storage/logs/pm2-scheduler-out.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss',
      env: {
        APP_ENV: 'production',
      },
    },
  ],
};
