# Uploaded file storage

Uploads are application data; Git does not include them. The `public/storage` link only exposes the configured public disk. It does not copy uploaded files into a new checkout or restore files after a deployment replaces the server's storage directory.

## Persistent local storage

For a single server, point `PUBLIC_STORAGE_PATH` at a writable directory outside the release/checkout directory. For example:

```dotenv
PUBLIC_STORAGE_PATH=/var/www/nutribuddy-shared/storage/app/public
```

Create that directory and grant the PHP/web-server user write access. Before switching an existing installation to this path, copy the current `storage/app/public` contents into it once. Set the same path for every release, then run:

```sh
php artisan config:clear
php artisan storage:link --force
```

For multiple application servers, this directory must be a shared persistent mount visible at the same path to every server. A local directory on each node will still result in different files per node. Keep backups of this directory with the database backups.

On Windows, use a stable directory outside the checkout, such as `D:/nutribuddy-shared/public`, and set the PHP process identity's permissions on it.

## Verify after deployment

Upload a test image in the admin area, confirm its database path is relative (for example `products/<generated-name>.jpg`), then open `/storage/products/<generated-name>.jpg`. Confirm it still loads after a new release is deployed. Do not commit user uploads to Git.

The storage disk and the `/storage/...` application route both use `PUBLIC_STORAGE_PATH`, so the route can serve files even when the public symlink is unavailable. The link remains useful for web-server/static serving and follows the same configured target.
