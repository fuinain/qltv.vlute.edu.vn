### Cấu hình SSO

```
# File .env

KEYCLOAK_CLIENT_ID=vlute.edu.vn
KEYCLOAK_CLIENT_SECRET=
KEYCLOAK_BASE_URL=https://sso.vlute.edu.vn/auth/
KEYCLOAK_REALM=Dev
KEYCLOAK_REDIRECT_URI=http://qltv.local/login/callback
```
```
php artisan ser --host="qltv.local" --port="80"
```
