# RRR Profile Edit Production API

Flutter uses this endpoint:

`POST /api/v1/profile/update`

Supported fields:

- `name`
- `description` / `bio`
- `country`
- `gender`
- `language`
- `dp` image file
- `profile_photos[]` up to 5 image files

Response:

```json
{
  "success": true,
  "user": {
    "name": "Abhishek",
    "bio": "My profile description",
    "dp": "https://domain.com/storage/profiles/dp/file.jpg",
    "profile_photos": ["https://domain.com/storage/profiles/gallery/file.jpg"]
  }
}
```

Run once on Laravel server:

```bash
php artisan storage:link
```

Real ID must not be editable from the profile edit page. It remains generated uniquely by Laravel/MongoDB Atlas.
