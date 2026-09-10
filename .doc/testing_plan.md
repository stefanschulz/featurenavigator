# Feature Navigator – Testing Plan

## Overview
This document describes the complete testing infrastructure that has been added to the **Feature Navigator** PrestaShop module. It follows the conventions used by the PrestaShop community and provides everything needed to run unit and integration tests locally or in CI.

---
### 1️⃣ Environment preparation
```bash
# From the module root (P:/dev/featurenavigator)
composer require --dev phpunit/phpunit ^9
composer require --dev prestashop/testing-framework   # provides PrestaShop‑specific TestCase helper
```
The `composer.json` file now contains these dev requirements.

---
### 2️⃣ Directory layout
```
featurenavigator/
├─ tests/
│  ├─ Unit/            # pure PHP unit tests (no PrestaShop bootstrap)
│  └─ Integration/    # tests that need the full PrestaShop environment
```
Both directories have been created.

---
### 3️⃣ PHPUnit configuration (`phpunit.xml.dist`)
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php"
         colors="true"
         stopOnFailure="false"
         failOnRisky="true"
         failOnWarning="true"
         verbose="true">
    <testsuites>
        <testsuite name="Unit"><directory>./tests/Unit</directory></testsuite>
        <testsuite name="Integration"><directory>./tests/Integration</directory></testsuite>
    </testsuites>
    <php>
        <!-- Adjust PS_ROOT_DIR if your CI runs from a different location -->
        <env name="PS_ROOT_DIR" value="../.."/>
    </php>
</phpunit>
```
Placed at the repository root.

---
### 4️⃣ Bootstrap (`tests/bootstrap.php`)
```php
<?php
declare(strict_types=1);
// Composer autoloader – always present after `composer install`
require_once __DIR__ . '/../vendor/autoload.php';

// Locate the PrestaShop installation (the module lives inside a shop).
$psRoot = realpath(__DIR__ . '/../../..'); // <shop_root>
if (!$psRoot || !file_exists($psRoot . '/config/config.inc.php')) {
    fwrite(STDERR, "Cannot find PrestaShop config.inc.php – aborting tests.\n");
    exit(1);
}
require_once $psRoot . '/config/config.inc.php';

// Force development mode to avoid caching side‑effects.
if (!defined('_PS_MODE_DEV_')) {
    define('_PS_MODE_DEV_', true);
}

// Load the module entry point so its PSR‑4 namespace is registered.
require_once __DIR__ . '/../featurenavigator.php';
```
This file boots the full PrestaShop environment before any test runs.

---
### 5️⃣ Sample Unit Test (`tests/Unit/SourceOptionsTest.php`)
```php
<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
class SourceOptionsTest extends TestCase
{
    /** @dataProvider letters */
    public function testAdjustValue(string $input, string $expected): void
    {
        $this->assertSame($expected, SourceOptions::adjustValue($input));
    }
    public static function letters(): array
    {
        return [
            ['A', 'a'],
            ['z', 'z'],
            ['1', '#'],
            ['',  '#'],
        ];
    }
}
```
Verifies the pure‑PHP helper `SourceOptions::adjustValue()`.

---
### 6️⃣ Sample Integration Test (`tests/Integration/ListControllerTest.php`)
```php
<?php
declare(strict_types=1);
use PrestaShop\Testing\PhpUnit\TestCase as PsTestCase;
use PrestaShop\Module\FeatureNavigator\Controller\FeatureNavigatorListModuleFrontController;
class ListControllerTest extends PsTestCase
{
    public function testInitContentAssignsVariables(): void
    {
        $_GET['letter'] = 'b';
        $controller = new FeatureNavigatorListModuleFrontController();
        $controller->initContent();
        $smarty = $this->getContext()->smarty;
        $this->assertSame('b', $smarty->getTemplateVars('letter'));
        $this->assertIsArray($smarty->getTemplateVars('entries'));
    }
}
```
Instantiates the front controller, runs `initContent()`, and checks that expected Smarty variables are set.

---
### 7️⃣ Local verification steps
```bash
# Install dev dependencies (if not already done)
composer install

# Run unit tests only
vendor/bin/phpunit --testsuite Unit

# Run integration tests (requires a reachable PrestaShop installation)
vendor/bin/phpunit --testsuite Integration
```
Both commands should finish with an **OK** status.

---
### 8️⃣ CI configuration (GitHub Actions) – optional but recommended
Create `.github/workflows/phpunit.yml`:
```yaml
name: PHP Tests
on: [push, pull_request]
jobs:
  phpunit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: mbstring,intl,zip
      - run: composer install --prefer-dist --no-progress
      - run: vendor/bin/phpunit   # reads phpunit.xml.dist automatically
```
The workflow will execute the same test suites on every PR.

---
### 9️⃣ Documentation update
A short *Running tests* paragraph has been added to `README.md` (see the accompanying change document).

---
## Change Document (for future agents)
**File:** `.doc/progress.md`
```
# Progress – Feature Navigator testing setup

- **composer.json**: added dev requirements `phpunit/phpunit ^9` and `prestashop/testing-framework`.
- Added folder **tests/** with subfolders **Unit** and **Integration**.
- Created **phpunit.xml.dist** (root) – defines two test suites and bootstrap path.
- Implemented **tests/bootstrap.php** to load Composer, PrestaShop core (`config.inc.php`) and the module entry point.
- Added a unit test **tests/Unit/SourceOptionsTest.php** covering `SourceOptions::adjustValue`.
- Added an integration test **tests/Integration/ListControllerTest.php** that boots the full environment and checks controller behaviour.
- Updated **README.md** with a *Running tests* section (see diff).
- (Optional) prepared CI workflow file `.github/workflows/phpunit.yml`.

All new files are placed under version control; no production code was modified. The repository now contains a complete, PrestaShop‑compatible testing scaffold ready for further test development.
```
---
### Next steps for contributors
1. Add more unit tests for pure‑PHP helpers (e.g., `SourceOptions::getSql`).
2. Expand integration tests to cover the product search provider and configuration form handling.
3. Keep the CI workflow up‑to‑date if additional tools (phpstan, cs‑fixer) are added later.

---
**End of document**
```