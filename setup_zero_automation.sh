#!/usr/bin/env bash
# Setup Zero automation: run as root
set -e
USER=zero
BASE_DIR=/opt/zero

# 1. Create user
id -u $USER &>/dev/null || useradd -m -s /bin/bash $USER
chown -R $USER:$USER $BASE_DIR || true

# 2. Install dependencies (Debian/Ubuntu)
apt-get update -y
apt-get install -y git tmux inotify-tools php8.2-cli php8.2-sqlite3 php8.2-mbstring php8.2-xml php8.2-curl

# 3. tmpfs for cache
mkdir -p $BASE_DIR/cache
mountpoint -q $BASE_DIR/cache || echo "tmpfs $BASE_DIR/cache tmpfs size=12G,mode=0775 0 0" >> /etc/fstab
mount -a

# 4. systemd build-farm template
cat >/etc/systemd/system/build-farm@.service <<'EOF'
[Unit]
Description=Zero Build-Farm sandbox %i
After=network.target

[Service]
Type=simple
User=zero
WorkingDirectory=/opt/zero
ExecStart=/opt/zero/scripts/sandbox-spawn.sh
Restart=always
RestartSec=2

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
for i in 1 2 3 4; do systemctl enable --now build-farm@$i.service; done

# 5. CI Trigger service
cat >/etc/systemd/system/ci-trigger.service <<'EOF'
[Unit]
Description=Zero CI Trigger (post-receive)

[Service]
Type=oneshot
User=zero
WorkingDirectory=/opt/zero
Environment=SPLIT_MAX_MIN=10
ExecStart=/opt/zero/scripts/rebase_loop.sh
EOF
systemctl daemon-reload

# 6. Git hook post-receive
sudo -u $USER mkdir -p $BASE_DIR/.git/hooks
cat >$BASE_DIR/.git/hooks/post-receive <<'HOOK'
#!/usr/bin/env bash
systemctl start ci-trigger.service
HOOK
chmod +x $BASE_DIR/.git/hooks/post-receive

# 7. Kanban watch service
cat >/etc/systemd/system/kanban-watch.service <<'EOF'
[Unit]
Description=Watch Kanban and run auto_split & auto_balancer

[Service]
Type=simple
User=zero
WorkingDirectory=/opt/zero
ExecStart=/usr/bin/inotifywait -m -e close_write zero-docs/Kanban.md | while read; do SPLIT_MAX_MIN=10 python3 scripts/auto_split.py zero-docs/Kanban.md; bash scripts/auto_balancer.sh; done
Restart=always

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable --now kanban-watch.service

# 8. Initial DB snapshot and base image
sudo -u $USER bash -c "cd $BASE_DIR && scripts/snapshot_db.sh create"
sudo -u $USER bash -c "cd $BASE_DIR && scripts/create_base_image.sh"

# 9. Cron job to clean old artefacts
cat >/etc/cron.daily/zero-artifacts-clean <<'EOF'
#!/usr/bin/env bash
find /opt/zero/archive -name "release-*.zip" -mtime +7 -delete
EOF
chmod +x /etc/cron.daily/zero-artifacts-clean

echo "Zero automation setup complete."