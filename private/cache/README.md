# Cache Directory

This directory contains cached data and temporary files.

**Security Notice:** This directory is protected by .htaccess rules to prevent direct web access.

## Cache Files

- Rate limiting data
- Session data (if file-based sessions)
- Compiled templates
- Application cache

## Maintenance

Regularly clean this directory to prevent it from growing too large:

```bash
# Example cleanup script
find /path/to/cache -type f -mtime +7 -delete
```