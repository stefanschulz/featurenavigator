# Progress – Feature Navigator testing setup

- **composer.json**: added dev requirement `phpunit/phpunit ^9` (Phase 1 completed).
- Added folder **tests/** with subfolders **Unit** and **Integration** (Phase 2 completed). *(Integration folder is currently empty but kept for future tests.)*
- Added **phpunit.xml.dist** inside `featurenavigator/` – defines Unit and Integration test suites and bootstrap path (Phase 3 completed).
- Implemented **tests/bootstrap.php** to load Composer and define a dummy `_PS_VERSION_` constant for unit tests (Phase 4 completed).
- Added a unit test **tests/Unit/SourceOptionsTest.php** covering `SourceOptions::adjustValue` (Phase 5 completed).
- Updated **README.md** with a *Testing* section and usage instructions (Phase 7 completed).
- Fixed CI workflow to run Composer and PHPUnit inside the `featurenavigator` subfolder (Phase 8 completed).
- Removed temporary **SimpleTest.php** used for debugging (cleanup step).

All new files are placed under version control; no production code was modified. The repository now contains a functional testing scaffold ready for further development.
