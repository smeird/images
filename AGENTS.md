# AGENTS.md

## Purpose
This repository contains the planning and implementation artifacts for an astronomy image showcase website. All contributors (human and AI agents) should keep documentation and implementation tightly synchronized.

## Mandatory workflow for every code/content change
1. **Understand scope first**
   - Read `README.md`, `WEBSITE_TASKS.md`, and any architecture notes before making edits.
2. **Update docs in the same change**
   - If behavior, routes, data model, UI, build steps, deployment, security, or tooling changes, you **must update `README.md` in the same commit**.
3. **Keep diagrams current**
   - `README.md` must include and maintain Mermaid diagrams representing:
     - user-facing flow
     - admin upload flow
     - high-level system architecture
   - If implementation changes affect any flow/component, update the relevant Mermaid diagram(s).
4. **Traceability requirement**
   - In your commit message and PR body, include a short note that README + diagrams were reviewed/updated.
5. **No stale docs policy**
   - A change that alters behavior but does not update docs is considered incomplete.

## README quality standards
`README.md` should always include:
- Project purpose and current maturity/status
- Local development steps
- Runtime/build assumptions
- Security notes for admin/backdoor features
- Folder/file map for key docs
- Mermaid diagrams (architecture + flows)
- A "Keeping docs in sync" section with explicit contributor expectations

## Change management checklist (required)
Before finalizing a change, verify:
- [ ] Relevant task docs are updated (`WEBSITE_TASKS.md`, etc.)
- [ ] `README.md` reflects latest behavior
- [ ] Mermaid diagrams still match implementation
- [ ] Any new env vars/config are documented
- [ ] Any new security-sensitive behavior is documented

## Testing and validation guidance
- Run lightweight checks relevant to the files changed.
- **Do not run tests that require a database connection/access** in this environment unless explicitly requested by the user.

## Commit and PR expectations
- Keep commits focused and descriptive.
- PRs should summarize:
  - what changed
  - why it changed
  - documentation updates made (including diagrams)
  - validation performed

## Scope
This file applies to the entire repository rooted at this directory.
