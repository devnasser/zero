PROCS:=$(shell nproc --all)
PARA=$(shell echo $$(( $(PROCS) * 2 )))

test:
	vendor/bin/pest --parallel --processes=$(PARA)

analyse:
	vendor/bin/phpstan analyse --memory-limit=2G --no-progress

build:
	bash scripts/build.sh

release:
	bash scripts/release.sh