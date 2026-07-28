# EnPharChem — Deployment Guide

One Docker image serves **both** cloud and on-premise. The application is
served under the `/enpharchem` base path in every mode, so all internal links
keep working without a rewrite.

| Mode | Database | Licensing | Compose file |
|------|----------|-----------|--------------|
| On-premise | Bundled MariaDB container | Online portal **or** offline signed file | `docker-compose.yml` |
| Cloud | Managed MySQL/MariaDB (RDS / Azure / Cloud SQL) | Online portal | `docker-compose.cloud.yml` |
| Local dev | XAMPP (unchanged) | Online portal | — (no Docker) |

Config is entirely environment-driven (`config/app.php`, `config/database.php`);
a bare XAMPP checkout still works via the original fallback defaults.

---

## 1. On-premise (self-contained)

```bash
cp .env.example .env          # set DB_PASS etc.
docker compose up -d --build
# → http://localhost:8080/enpharchem
```

The entrypoint waits for MariaDB and imports the schema
(`schema.sql`, `control_panel_tables.sql`, `training_assessment_tables.sql`,
`licensing_tables.sql`) on first boot only. Data persists in the `db_data`
volume.

## 2. Cloud (managed database)

```bash
cp .env.example .env          # set APP_URL + managed DB_HOST/USER/PASS
docker compose -f docker-compose.cloud.yml up -d --build
```

Point `DB_HOST` at your managed instance and `APP_URL` at your public URL
(e.g. `https://app.example.com/enpharchem`). The image is stateless — scale it
horizontally behind a load balancer; all state lives in the managed DB.
A container healthcheck probes `/enpharchem/login`.

---

## 3. License enforcement

`run()` on the Flowsheet Simulator is gated at compute time
(`FlowsheetController::licenseState()`). Decision order:

1. `FLOWSHEET_LICENSE_ENFORCE=false` → all runs allowed (internal/eval build)
2. superuser/admin → allowed
3. Valid **offline signed license** granting the module → allowed
4. Active online license (`licenses` + `license_modules`) granting the module → allowed
5. `modules.license_waived = 1` (Module License Manager) → allowed
6. otherwise → **403, blocked** (audited to `license_audit_log`)

### Offline (air-gapped) licenses

For installs with no route to the online portal, ship a **vendor-signed**
license file. The install trusts only the bundled public key, so entitlements
cannot be forged.

**Vendor, once:**
```bash
openssl genrsa -out vendor_private.pem 2048
openssl rsa -in vendor_private.pem -pubout -out deploy/keys/vendor_public.pem
# keep vendor_private.pem secret; vendor_public.pem ships in the image
```

**Vendor, per customer:**
```bash
php lib/sign-license.php \
    --key=vendor_private.pem \
    --customer="Acme Refining" \
    --expiry=2027-12-31 \
    --seats=25 \
    --modules=flowsheet-simulator,process-sim-chemicals \
    --out=license.lic
# --modules=*  grants everything
```

**Customer:** drop `license.lic` into `deploy/license/` (mounted read-only to
`/var/lib/enpharchem/license.lic`) and restart. `LICENSE_FILE` /
`LICENSE_PUBKEY` env vars point the app at the file and key.

---

## 4. Notes & next steps

- **Secrets:** the compose files read DB credentials from `.env`; never commit
  `.env` or `*_private.pem` (both are gitignored).
- **Base path:** serving at web root (dropping `/enpharchem`) is a larger
  refactor (many absolute links) — tracked as a follow-up.
- **TLS:** terminate at your ingress/load balancer, or add a reverse proxy in
  front of the container.
