# StudentHub Practical 15 — Test Report

**Environment:** Windows + XAMPP (record PHP/MySQL versions used during your demonstration)  
**Tester:** ____________________  
**Date:** ____________________

> Mark each result only after performing the test in your local XAMPP environment. The code syntax check is performed during packaging; database and browser behavior must be verified locally.

| No. | Test case | Expected result | Actual result / evidence |
|---:|---|---|---|
| 1 | Open the home page | Page loads and navigation works | Pending local test |
| 2 | Submit registration with missing/invalid values | Validation message; no account inserted | Pending local test |
| 3 | Register with a new valid email | Account and student profile are created | Pending local test |
| 4 | Register again with the same email | Duplicate registration is rejected gracefully | Pending local test |
| 5 | Login with an incorrect password | Generic login error; no session established | Pending local test |
| 6 | Login with a valid student account | Dashboard opens | Pending local test |
| 7 | Open a protected page while logged out | Redirect to login | Pending local test |
| 8 | Login as student and open admin dashboard | Access denied / admin-only page not shown | Pending local test |
| 9 | Login as admin | Admin dashboard and audit history load | Pending local test |
| 10 | Register for an upcoming event | Registration saved and displayed | Pending local test |
| 11 | Register twice for the same event | Duplicate registration prevented | Pending local test |
| 12 | Update profile | Name/course updated in database | Pending local test |
| 13 | Submit contact form and feedback | Records saved to database | Pending local test |
| 14 | Add a student on Student Management | AJAX response succeeds and row appears without full page reload | Pending local test |
| 15 | Delete an unlinked student record | AJAX deletion succeeds; linked account record is protected | Pending local test |
| 16 | Toggle a user status from admin dashboard | Status changes and audit record is written | Pending local test |
| 17 | Submit a write request without a valid CSRF token | Request rejected | Pending local test |
| 18 | Resize browser to mobile width | Navigation and layout remain usable | Pending local test |
| 19 | Verify database schema in phpMyAdmin | Expected tables exist | Pending local test |
| 20 | Verify repository | README, SQL dump and project files are committed; no credentials/logs committed | Pending local test |

## Issues / observations

- ____________________________________________________________
- ____________________________________________________________

## Final conclusion

Complete this after testing: __________________________________
