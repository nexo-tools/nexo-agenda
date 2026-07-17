# Screenshots

Drop the README screenshots here. They're generated from the demo seeder so they
always match the current UI:

```bash
./vendor/bin/sail artisan migrate:fresh --seed --seeder=DemoSeeder
```

Capture and save as PNG:

| File | URL | Viewport |
|---|---|---|
| `public-page.png` | `/estudio-nexo` | 390×844 (mobile) |
| `booking-flow.png` | `/estudio-nexo/reservar/{service}` (horarios/datos step) | 390×844 (mobile) |
| `dashboard.png` | `/` logged in as `demo@nexoagenda.test` / `password` | 1440×900 |

Then uncomment the image table in the root [README](../../README.md#screenshots).
