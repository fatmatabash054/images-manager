# Technical Notes

## Biggest Docker problem and how I solved it

The web container started before MySQL had finished initialising, so the first
page load failed with "Database connection failed". `depends_on` alone only
waits for the container to *start*, not for MySQL to be *ready*.

**Fix:** added a `healthcheck` (`mysqladmin ping`) to the `db` service and
changed the web service to `depends_on: db: condition: service_healthy`.
The web container now starts only when the database accepts connections.

A second issue was uploads: files written by Apache (`www-data`) need a writable
folder, and a bind mount from the host breaks permissions. I used a named volume
(`uploads`) and `chown`ed the folder in the Dockerfile.

## Most important Git/GitHub lesson

Small, focused commits with clear messages (`feat:`, `docker:`, `docs:`, `fix:`)
make the history readable and let me undo one change without losing others.
Secrets (`.env`) and generated files (`uploads/`) belong in `.gitignore`
from the very first commit, because removing them from history later is painful.
