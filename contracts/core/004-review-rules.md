# Review Rules
## Contract ID: 004
## Version: 1.0.0

---

# Purpose

This contract defines the mandatory review process for every implementation task.

No task is considered complete until all review checks pass.

Implementation without review is forbidden.

---

# Review Authority

The AI must review its own implementation before returning the result.

If any review fails,

the AI must fix the issue before returning the final implementation.

---

# Review Order

Every task must be reviewed in this order.

1.

Scope Review

↓

2.

Architecture Review

↓

3.

Code Quality Review

↓

4.

Security Review

↓

5.

Performance Review

↓

6.

WordPress Standards Review

↓

7.

Final Validation

---

# Scope Review

Verify

Only requested files changed.

No unrelated files modified.

No future milestone implemented.

No hidden features added.

No unnecessary code added.

---

# Architecture Review

Verify

No architecture violations.

No layer violations.

No duplicated business logic.

No service misuse.

No helper misuse.

No provider misuse.

No circular dependencies.

---

# Coding Standards Review

Verify

Strict types enabled.

PSR-12 followed.

WordPress Coding Standards followed.

Meaningful naming.

No magic numbers.

No dead code.

No unused imports.

No unused variables.

No duplicated logic.

---

# Security Review

Verify

Sanitization complete.

Escaping complete.

Capability checks complete.

Nonce verification complete.

No SQL injection risk.

No XSS risk.

No CSRF risk.

No unsafe output.

---

# Performance Review

Verify

No duplicate queries.

No duplicate loops.

No unnecessary object creation.

No unnecessary hooks.

No unnecessary loading.

No expensive processing inside loops.

---

# Translation Review

Verify

Every user-facing string is translatable.

Correct text domain used.

No hardcoded UI text.

---

# WordPress Review

Verify

WordPress APIs used where appropriate.

WooCommerce APIs used where appropriate.

No deprecated APIs.

No direct database manipulation without approval.

---

# Documentation Review

Verify

PHPDoc added where required.

Complex code explained.

Public methods documented.

---

# Dependency Review

Verify

No new dependency introduced.

No package changes.

No composer changes.

Unless explicitly requested.

---

# Git Review

Verify

Commit scope respected.

Commit message follows Conventional Commits.

No unrelated formatting changes.

---

# Forbidden

Never approve code that contains

TODO

FIXME

var_dump()

print_r()

die()

exit()

Debug statements.

Unused methods.

Unused classes.

Unused files.

---

# Completion Rules

A task is complete only when

All review sections pass.

If one section fails,

the task fails.

---

# Output Format

Every implementation must end with

## Review Report

Scope

PASS / FAIL

Architecture

PASS / FAIL

Coding Standards

PASS / FAIL

Security

PASS / FAIL

Performance

PASS / FAIL

Translation

PASS / FAIL

Documentation

PASS / FAIL

Overall

PASS / FAIL

---

# If Any Check Fails

Fix the issue.

Run the review again.

Return only the final corrected implementation.

Never return known issues.

---

END OF CONTRACT