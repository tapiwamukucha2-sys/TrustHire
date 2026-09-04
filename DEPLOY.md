# Deploying TrustHire (Render + Cloudflare R2)

This app is now ready to deploy on Render's free tier, with Cloudflare R2
handling admin-uploaded photos (hero/trust images, listing photo edits) so
they survive redeploys — Render's free web service disk is wiped on every
deploy and restart.

Everything below that touches an account dashboard (Render, Cloudflare,
GitHub) has to be done by you — an AI assistant can't sign up for services
or click through billing/verification screens on your behalf. This is the
exact click path.

## 1. Push the code to GitHub

Render deploys from a git repo (GitHub or GitLab).

```
cd trusthire-laravel
git remote add origin https://github.com/<your-username>/trusthire.git
git push -u origin main
```

(Create the empty repo on github.com first if you haven't — no README/license,
so it doesn't conflict with what's already here.)

## 2. Create a Cloudflare R2 bucket

1. Sign up / log in at https://dash.cloudflare.com
2. Go to **R2 Object Storage** → **Create bucket**. Name it e.g. `trusthire-uploads`.
3. Open the bucket → **Settings** → **Public access** → allow public access via
   the bucket's `r2.dev` subdomain. Copy that public URL — it's your `AWS_URL`.
4. Go to **R2** → **Manage API Tokens** → **Create API Token**. Give it
   read+write access to this bucket only. Copy the **Access Key ID** and
   **Secret Access Key** — you won't see the secret again.
5. Your **Account ID** is shown on the R2 overview page. Your S3-compatible
   endpoint is `https://<account-id>.r2.cloudflarestorage.com`.

You now have everything for these five values:
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_BUCKET` = `trusthire-uploads`
- `AWS_ENDPOINT` = `https://<account-id>.r2.cloudflarestorage.com`
- `AWS_URL` = the public `r2.dev` URL from step 3

## 3. Deploy to Render

1. Sign up / log in at https://render.com
2. **New** → **Blueprint** → connect your GitHub repo. Render reads
   `render.yaml` in this repo and provisions a free web service **and** a
   free Postgres database automatically.
   - If Render's blueprint doesn't recognize `runtime: php` (native PHP
     blueprint support has changed before), create the pieces manually
     instead: **New → Web Service** → connect the repo → Render should
     auto-detect PHP from `composer.json`. Set:
     - Build Command: `composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan storage:link && php artisan migrate --force && php artisan config:cache`
     - Start Command: `php artisan serve --host 0.0.0.0 --port $PORT`
     Then **New → PostgreSQL** separately and copy its connection details
     into the web service's environment variables (`DB_HOST`, `DB_PORT`,
     `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_CONNECTION=pgsql`).
3. In the web service's **Environment** tab, fill in the five R2 values from
   step 2 (the blueprint leaves them blank on purpose — `sync: false`).
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
- **R2 free tier**: 10 GB storage, no time limit, generous free egress — plenty
  for photos.
- **Render Postgres free tier**: check current retention terms in the Render
  dashboard when you create it; free database plans have changed over time.
- Your app is reachable at `https://trusthire.onrender.com` (or whatever name
  you pick) — a real HTTPS URL, no separate domain purchase needed to get
  started.

## Notes on what does and doesn't need persistent storage

- Listing photos and verification ID/selfie photos are stored as base64 data
  directly in the database — they're unaffected by disk persistence either way.
- Only admin-uploaded content (hero photos, "Why Choose TrustHire" photos,
  and photos replaced via Admin → Listings → Edit) actually writes to disk,
  which is why only those go through R2 (`UPLOADS_DISK=r2`).
