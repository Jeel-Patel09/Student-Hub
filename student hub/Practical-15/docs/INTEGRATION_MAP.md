# Integration map — Practical 15

## Source folders reviewed

- `Student-Hub Project.zip`: earlier static StudentHub pages and assets, covering the static site used around Practicals 3–6.
- `Practical-6.zip`: contains duplicate/iterative versions of Practical 3, Practical 4, Practical 5 and Practical 6. The stray `Practical1.pdf` was not included because Practical 1 and 2 are out of scope.
- `Practical-7.zip`: contains separate folders for Practical 7 through Practical 14. Its `Practical-15/` folder was empty.
- Previously available individual Practical 12, 13 and 14 ZIPs: compared with the corresponding folders from `Practical-7.zip`; these were substantially the same versions.

## What is used for the final portal

The root folder is the integrated app. It contains a shared navigation and layout, account registration/login, student dashboard, events and event registration, profile updates, contact/feedback forms, administrator dashboard, and an administrator-only AJAX student API.

The integrated app uses the dedicated `studenthub_final_db` database created by `database/studenthub_final_setup.sql`. The copied `admin/` dashboard has been configured to use the same database and PHP session as the root app.

## Preserved practicals

- `practicals/Practical-3` through `practicals/Practical-6` preserve the earlier static exercises.
- `practicals/Practical-7` through `practicals/Practical-14` preserve the separate PHP/database exercises.
- `legacy/static-site` preserves the submitted Student-Hub static project files for reference.

The historical practical folders remain standalone examples and may use their original database names. They are not imported by the final setup SQL. For the Practical 15 demonstration, open the integrated root portal and use only `database/studenthub_final_setup.sql`.

## Validation status

- PHP syntax check: all PHP files in the final package passed `php -l`.
- ZIP integrity: checked when the final archive is created.
- MySQL schema execution, browser interactions, responsive behavior and GitHub push: must still be verified in the user's local XAMPP/browser/GitHub environment. They are not marked as passed in `docs/TEST_REPORT.md` until performed there.
