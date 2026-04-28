---
name: Infrastructure checklist
about: Checklist for reviewing Docker, CI/CD and production readiness changes
title: "Infra: Docker & CI Review"
labels: infrastructure, chore
assignees: ''
---

Checklist
- [ ] Dockerfile security & non-root user
- [ ] Docker image size & caching
- [ ] Compose secrets & environment example
- [ ] GitHub Actions: CI runs tests
- [ ] GitHub Actions: CD pushes images securely
- [ ] Health endpoint and readiness probes
- [ ] Documentation updated (README.DOCKER.md)
