#!/bin/sh
set -e

echo "[start-script] Cleaning any existing MPM load files..."
rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf || true

# Ensure prefork is available
if [ ! -f /etc/apache2/mods-available/mpm_prefork.load ]; then
  if [ -f /usr/lib/apache2/modules/mod_mpm_prefork.so ]; then
    printf 'LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so\n' > /etc/apache2/mods-available/mpm_prefork.load
    echo "[start-script] Created mpm_prefork.load"
  else
    echo "[start-script] WARNING: mod_mpm_prefork.so not found"
  fi
fi

# Symlink into mods-enabled
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load || true

# Defensive disable other MPMs
a2dismod mpm_event mpm_worker || true

# Log enabled modules for debugging
echo "[start-script] enabled mods-enabled:" && ls -la /etc/apache2/mods-enabled || true
echo "[start-script] apache modules:" && apachectl -M || true

# Execute original entrypoint to start Apache
exec docker-php-entrypoint "$@"
