Upgrade plan: Laravel 5.5 -> modern LTS (recommend target: 10.x)

Date: 2025-10-14

Goals
- Move codebase to a supported Laravel version (10.x recommended) and modern PHP (8.1+ or 8.2/8.3) while minimizing downtime.
- Replace abandoned or incompatible packages.
- Keep backups and a reproducible environment for composer operations.

High-level approach
1. Establish reproducible environment
   - Use Docker or phpenv to run composer and test suites under the PHP version required by each upgrade step.
   - Keep `composer.lock` in version control; commit changes incrementally.

2. Create a branch: `upgrade/laravel-5.5-to-10`

3. Staged upgrades (recommended path)
   - 5.5 -> 5.6/5.7 (small)
   - 5.7 -> 5.8
   - 5.8 -> 6.x LTS (switch to semantic versioning, some namespace changes)
   - 6.x -> 7.x (follow upgrade guide)
   - 7.x -> 8.x
   - 8.x -> 9.x LTS
   - 9.x -> 10.x LTS

   Each major step: read Laravel's upgrade guide, run tests, fix deprecations.

4. Replace or upgrade key packages
   - Replace `fzaninotto/faker` with `fakerphp/faker`.
   - Replace `laravelcollective/html` (if needed) with `spatie/laravel-html` or update to a maintained fork compatible with Laravel 10.
   - Update `phpunit/phpunit` progressively to match the PHP version used (phpunit 12 for PHP 8.1+).
   - Review `nwidart/laravel-modules` upgrade path (major jumps exist); test modules individually.
   - `spatie/laravel-permission` upgrade: follow their migration docs (model/guard changes may be required).
   - Update yajra datatables to the compatible major version.

5. Automated checks
   - Add GH Actions or GitLab CI with job matrix: PHP 7.4 (if needed), 8.1, 8.2; run `composer install`, `php -l`, `phpunit`.

6. Manual code changes likely required
   - Auth scaffolding changes (Laravel 6+ switched some auth scaffolding out of core).
   - Replace `middleware` key behavior and route registration differences.
   - Update `collective/html` usage to either new package or blade components.
   - Handle deprecated helper functions and changes in container binding names.

7. Database and migration considerations
   - Backup DB before migrations.
   - Run migrations locally after upgrades.

8. Timeline & testing
   - Small teams: 2-6 weeks depending on test coverage and custom code.
   - Priority: ensure test coverage for critical flows (pos, transactions, imports, reports).

Commands and environment notes
- Use Docker for reproducible composer runs (example to run composer under PHP 7.4):

  docker run --rm -v "$PWD":/app -w /app php:7.4-cli bash -lc "apt-get update && apt-get install -y unzip git && php -r 'copy(\"https://getcomposer.org/installer\", \"composer-setup.php\");' && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && composer update --with-all-dependencies --no-interaction"

- For local development, set up `.php-version` or `.nvmrc` equivalents and Docker Compose.

Notes & risks
- Major package upgrades will likely require code changes; plan to test payment, invoicing, and import flows first.
- Licensing/legal: verify ownership of this copy before public deployment.

If you want, I can produce a detailed file listing of packages to change and a step-by-step PR plan for the first stage (5.5 -> 5.8)."}]}``
