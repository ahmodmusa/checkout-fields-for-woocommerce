# Task Contract

## Task ID

T0004

---

## Task Name

Plugin Kernel

---

## Objective

Create the main Plugin kernel class.

This class will become the application's entry point.

At this stage it must remain an empty skeleton.

No business logic.

No hooks.

No services.

No initialization.

---

## Required Reading

contracts/core/000-project-contract.md

contracts/core/001-architecture-contract.md

contracts/core/002-coding-standards.md

contracts/core/003-project-conventions.md

contracts/core/004-review-rules.md

contracts/core/005-task-template.md

---

## Allowed Files

src/Plugin.php

composer.json (ONLY if PSR-4 autoload must be updated)

---

## Forbidden Files

checkout-fields-for-woocommerce.php

assets/

languages/

tests/

docs/

contracts/

Any other file.

---

## Requirements

Create

src/Plugin.php

The class must:

Use namespace

SCFW

Be declared

final

Contain ONLY one public static method

boot()

boot() must

return void

have an empty body

No constructor.

No properties.

No constants.

No hooks.

No services.

No helper methods.

No singleton.

No initialization logic.

No comments inside methods.

No TODO markers.

No dead code.

Use

declare(strict_types=1);

Include proper PHPDoc.

---

## Acceptance Criteria

✓ src/Plugin.php created

✓ Namespace correct

✓ Final class

✓ Static boot() method exists

✓ Empty implementation

✓ WPCS compliant

✓ PSR-12 compliant

✓ No other files modified (except composer.json if required)

---

## Deliverables

src/Plugin.php

composer.json (only if needed)

---

## Commit Message

feat: add plugin kernel

---

## Review Report

Include

Scope

Architecture

Coding Standards

Security

Performance

Overall

PASS / FAIL

---

If any architectural uncertainty exists

STOP

Output

ARCHITECT DECISION REQUIRED

Do not continue.

END TASK