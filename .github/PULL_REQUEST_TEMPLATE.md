## Summary

<!-- What does this PR change, and why? Link any related issue (Fixes #123). -->

## Type of change

- [ ] Bug fix
- [ ] New feature
- [ ] Documentation
- [ ] Refactor / chore

## Checklist

- [ ] Pint passes (`vendor/bin/pint --test`)
- [ ] Larastan passes (`vendor/bin/phpstan analyse --no-progress`)
- [ ] Pest passes (`vendor/bin/pest`), with tests for new behavior
- [ ] Translations check passes (`node scripts/generate-translations.mjs --check`) if I added `__()` strings
- [ ] `npm run build` succeeds
- [ ] No secrets, real customer data, or committed `.env`
- [ ] Commits follow Conventional Commits

## Notes for reviewers

<!-- Anything reviewers should focus on, screenshots for UI changes, etc. -->
