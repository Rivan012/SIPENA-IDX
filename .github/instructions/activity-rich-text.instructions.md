---
description: "Use when changing the Tiptap activity-description editor, HTML sanitization, description image upload or serving, activity and planning description forms, or related tests."
---

# Activity Description Rich Text

- This feature is already implemented. Extend its existing components and flows; do not add a second editor, sanitizer, or parallel upload route.
- Check `composer.json` and `composer.lock` for PHP package versions and `package.json` and the lockfile for JavaScript versions before relying on APIs or changing dependencies. Confirm runtime versions when the task requires them.
- Main implementation: `resources/js/Components/RichTextEditor.vue`, `resources/js/Components/RichTextContent.vue`, `app/Support/ActivityDescriptionSanitizer.php`, `config/purifier.php`, and `app/Http/Controllers/ActivityDescriptionImageController.php`.
- The shared editor is used by maker activity forms, admin activity forms, and planning forms. Planning descriptions are stored on `DetailKegiatan`, not `RencanaKegiatan`; include its controller and detail editor when changing planning behavior.
- Sanitize descriptions on every write and before display, including legacy content and approval copies. Use the centralized sanitizer and the `activity_description` Purifier allowlist. Never render editor or database HTML directly with `v-html`; pass sanitized content through `RichTextContent.vue`.
- Keep image sources limited to application-uploaded files that exist on the private `local` disk. Do not allow base64/data URIs or external image URLs. Preserve upload validation and authorization on retrieval; an image URL alone does not prove access. Do not move these files to public storage without an explicit security review.
- The first valid image can serve as the activity-list banner. Reuse the sanitizer's image lookup and render a banner only when the referenced file exists.
- Do not add a database migration for descriptions; use the existing description columns. Preserve readable display of legacy plain-text descriptions.
- Extend `tests/Feature/ActivityDescriptionTest.php` for sanitizer, persistence/display flows, approval copying, upload validation, and image authorization. Run `php artisan test tests/Feature/ActivityDescriptionTest.php`; run the frontend build when editor or styling code changes.
