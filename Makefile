.PHONY: help setup dev check lint data-check

help:
	@echo "Available commands:"
	@echo "  make setup      Create local config and writable runtime directories"
	@echo "  make dev        Start the PHP development server"
	@echo "  make check      Run all fast local validation"
	@echo "  make lint       Check PHP syntax"
	@echo "  make data-check Validate committed JSON data"

setup:
	@./scripts/setup.sh

dev:
	@./scripts/dev.sh

check:
	@./scripts/check.sh

lint:
	@./scripts/check.sh --lint-only

data-check:
	@./scripts/check.sh --data-only
