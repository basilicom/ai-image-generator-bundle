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
