# Development Workflow

Version: 1.0.0

Status: Architecture Freeze

---

# Purpose

This document defines the official software development workflow for the project.

Every implementation task must follow this workflow.

No contributor, human or AI, may bypass these rules.

---

# Development Philosophy

The project follows:

- Architecture First
- Contract First
- Task Driven Development
- Review Before Commit
- Small Incremental Changes

The architecture is frozen.

Implementation evolves.

Architecture does not.

---

# Repository Branches

main

Stable production branch.

Never develop directly on main.

develop

Primary development branch.

All implementation work happens here.

---

# Development Order

Every feature follows this sequence.

Architecture

↓

Contract

↓

Task

↓

Implementation

↓

Review

↓

Commit

↓

Push

↓

Next Task

Skipping any step is prohibited.

---

# Task Rules

Every task must:

- Solve one problem only.
- Produce one logical Git commit.
- Avoid future milestone implementation.
- Respect all contracts.
- Respect all architecture documents.

---

# Implementation Rules

Implementation must:

- Modify only approved files.
- Avoid unrelated refactoring.
- Preserve project architecture.
- Keep methods small and focused.
- Prefer readability over cleverness.

Implementation must NOT:

- Redesign architecture.
- Rename modules.
- Move folders.
- Introduce unnecessary abstractions.
- Add speculative features.

---

# AI Rules

AI contributors must:

Read before writing.

Understand before modifying.

Respect architecture.

Respect contracts.

Respect milestones.

If implementation appears to require architectural change,

STOP

Output exactly

ARCHITECT DECISION REQUIRED

Never redesign the project.

Never change frozen documentation.

Never invent new architecture.

---

# Coding Rules

Follow

- WordPress Coding Standards
- PSR-12
- Strict Types

One public class per file.

One responsibility per class.

One logical change per commit.

---

# Review Rules

Every implementation requires review.

The review must verify

Scope

Architecture

Coding Standards

Security

Performance

Backward Compatibility

Overall

PASS / FAIL

No commit before PASS.

---

# Commit Rules

One task

=

One commit.

Commit messages follow Conventional Commits.

Examples

feat:

fix:

docs:

refactor:

test:

chore:

No mixed-purpose commits.

---

# Git Workflow

Development

develop

↓

Review

↓

Stable

main

↓

Release

No direct commits to main.

---

# Documentation Rules

Contracts define mandatory rules.

Architecture documents define permanent design.

Tasks define implementation work.

README explains the project.

Documentation must remain synchronized with implementation.

---

# Error Handling

If a task cannot be completed safely

STOP.

Do not guess.

Do not redesign.

Return

ARCHITECT DECISION REQUIRED

---

# Refactoring Policy

Refactoring is allowed only when

- Explicitly requested.

OR

- Required to complete the current task safely.

Large refactoring requires architectural approval.

---

# Definition of Done

A task is complete only when

- Implementation complete
- Review PASS
- PHPCS PASS
- PHP lint PASS
- Documentation updated (if required)
- Commit created
- Push completed

---

# Architecture Protection

The following documents are frozen

01-project.md

02-architecture.md

03-milestones.md

04-directory-structure.md

05-class-diagram.md

06-request-lifecycle.md

07-data-model.md

08-task-dependency-map.md

Changes require architectural approval.

---

# AI Prompt Template

Every implementation prompt should begin with:

"The architecture is frozen.

Read all contracts before implementation.

Implement only the requested task.

Do not redesign the project.

If architecture changes appear necessary,

STOP and output:

ARCHITECT DECISION REQUIRED."

---

END OF DOCUMENT