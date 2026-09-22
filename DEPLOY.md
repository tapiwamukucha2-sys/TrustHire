# Deploying TrustHire (Render + Neon)

This app is ready to deploy on Render's free tier, with **Neon** for
Postgres (permanently free — Render's own free Postgres plan expires after
30 days, Neon's doesn't).

Admin-uploaded photos (hero/trust images, listing photo edits) currently go
to Render's own local disk, which is wiped on every deploy and restart —
Cloudflare R2 would fix that, but it requires a payment card on file even
for its free tier, so it's skipped for now. Practical effect: after any
redeploy, re-upload the hero/trust photos via Admin → Site Content. Listing
photos and verification documents are stored as base64 in the database and
are unaffected either way. See "Adding R2 later" at the bottom if this
becomes annoying.

Everything below that touches an account dashboard (Render, Neon, GitHub)
has to be done by you — an AI assistant can't sign up for services or click
through billing/verification screens on your behalf. This is the exact
click path.

## 1. Push the code to GitHub

Done — this repo is pushed to `github.com/tapiwamukucha2-sys/TrustHire`.

## 2. Create a Neon Postgres database

1. Sign up / log in at https://console.neon.tech
2. **Create a project** → name it `trusthire` → pick any region.
3. On the project dashboard, find the **Connection string** — Neon shows it
   as one URL like `postgresql://user:password@ep-xxxx.neon.tech/dbname?sslmode=require`.
4. Break that URL into its parts for Render's environment variables:
   - `DB_HOST` = the `ep-xxxx...neon.tech` part
   - `DB_PORT` = `5432`
   - `DB_DATABASE` = the database name (usually `neondb`)
   - `DB_USERNAME` = the user
   - `DB_PASSWORD` = the password
   (`DB_SSLMODE=require` is already set in `render.yaml` — Neon requires SSL.)

## 3. Deploy to Render

1. Sign up / log in at https://render.com
2. **New** → **Blueprint** → connect your GitHub repo (`TrustHire`). Render
   reads `render.yaml` and provisions the free web service.
   - If Render's blueprint doesn't recognize `runtime: php` (native PHP
     blueprint support has changed before), create it manually instead:
     **New → Web Service** → connect the repo → Render should auto-detect
     PHP from `composer.json`. Set:
     - Build Command: `composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan storage:link && php artisan migrate --force && php artisan config:cache`
     - Start Command: `php artisan serve --host 0.0.0.0 --port $PORT`
3. In the web service's **Environment** tab, fill in the five `DB_*` values
   from step 2 (Neon), plus `DB_CONNECTION=pgsql` and `DB_SSLMODE=require`
   if the blueprint didn't already set them (the blueprint leaves them
   blank on purpose — `sync: false`).
4. Set `APP_URL` to your Render URL once it's assigned, e.g.
   `https://trusthire.onrender.com` — this is what makes hero/photo URLs and
   login redirects resolve correctly.
5. Deploy. First deploy runs migrations automatically via the build command.

## 4. Create your admin login

Once deployed, open a **Shell** for the web service from the Render
dashboard and run:

```
php artisan tinker --execute '
$u = App\Models\User::updateOrCreate(
    ["username" => "Trust"],
    ["name" => "Trust Admin", "password" => Hash::make("YOUR-OWN-PASSWORD"), "is_admin" => true, "profile_completed_at" => now(), "terms_accepted_at" => now()]
);
echo $u->id;
'
```

Pick your own password here rather than reusing the one from local dev.

## 5. What to expect on the free tier

- **Cold starts**: Render's free web service sleeps after ~15 minutes idle.
  The first visit after that takes ~30 seconds to wake up — normal, not a bug.
- **Neon free tier**: permanently free, no 30-day expiry (unlike Render's own
  Postgres). Neon's free compute also idles after inactivity and auto-resumes
  on the next query — a few hundred ms, much faster than Render's cold start.
- **Uploaded photos don't survive a redeploy** (see the note at the top) —
  re-upload hero/trust photos via Admin → Site Content after each deploy
  until R2 (or another persistent disk) is wired up.
- Your app is reachable at `https://trusthire.onrender.com` (or whatever name
  you pick) — a real HTTPS URL, no separate domain purchase needed to get
  started.

## Adding R2 later

If losing uploaded photos on redeploy becomes annoying, R2's free tier
(10GB, no time limit, zero egress fees) just needs a card on file to
activate — no charge unless you exceed those limits. Once that's set up:

1. **R2 Object Storage** → **Create bucket** → name it `trusthire-uploads`
2. Bucket → **Settings** → **Public access** → enable via the `r2.dev`
   subdomain → copy that URL (`AWS_URL`)
3. **Manage API Tokens** → **Create API Token** → Object Read & Write,
   scoped to that bucket → copy the Access Key ID and Secret Access Key
4. Account ID is on the R2 overview page → endpoint is
   `https://<account-id>.r2.cloudflarestorage.com`
5. In Render's environment variables, set `UPLOADS_DISK=r2` and the five
   `AWS_*` values above — the app already supports this, no code changes
   needed (see `config/filesystems.php`).
