init-infra:
	sudo apt-get update -y && sudo apt-get install -y podman fuse-overlayfs python3-pip && \
	pip install --break-system-packages podman-compose

build-image:
	podman build -t localhost/team-base:1.0 -f Dockerfile .

up: build-image
	podman-compose up -d

down:
	podman-compose down