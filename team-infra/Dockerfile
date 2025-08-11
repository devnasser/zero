FROM ubuntu:25.04

# Install required packages
RUN apt-get update -y && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    python3 python3-pip nodejs npm build-essential git && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

ENV LANG=C.UTF-8 \
    LC_ALL=C.UTF-8

WORKDIR /workspace