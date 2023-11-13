#!/usr/bin/make -f

.PHONY: unit
unit:
	./vendor/bin/phpunit --testdox --testsuite unit

.PHONY: yarn-watch
yarn-watch:
	yarn watch

.PHONY: yarn-build
yarn-build:
	yarn build

.PHONY: ollama-stop
ollama-stop:
	@cd docker && docker-compose down

.PHONY: ollama-start
ollama-start:
	$(MAKE) ollama-stop
	@cd docker && docker-compose up --build -d
