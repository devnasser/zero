init-infra:
	sudo apt-get update -y && sudo apt-get install -y docker.io fuse-overlayfs docker-compose-plugin && \
	sudo mkdir -p /etc/docker && \
	echo '{"storage-driver":"fuse-overlayfs","features":{"buildkit":true},"icc":false}' | sudo tee /etc/docker/daemon.json && \
	sudo systemctl enable --now docker && sudo systemctl restart docker

build-image:
	sudo docker build -t team-base:1.0 -f Dockerfile .

up: build-image
	sudo docker compose up -d

down:
	sudo docker compose down