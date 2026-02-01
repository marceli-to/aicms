# AICMS

AI-powered CMS for Laravel — Edit static content via chat.

## Installation

```bash
composer require marceli-to/aicms
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=aicms-config
```

Add your Anthropic API key to `.env`:

```env
AICMS_ANTHROPIC_KEY=sk-ant-...
AICMS_MODEL=claude-sonnet-4-20250514
```

Edit `config/aicms.php` to define which files are editable:

```php
'editable_paths' => [
    'resources/views/pages/*.blade.php',
    'content/**/*.md',
],
```

## Usage

1. Make sure you have authentication set up in your Laravel app
2. Visit `/aicms` (or your configured route prefix)
3. Start chatting to edit content

### Example prompts

- "Change the homepage title to 'Welcome to Our Site'"
- "Update the contact email to info@example.com"
- "List all editable files"
- "Show me the about page content"

## Middleware

By default, AICMS uses `['web', 'auth']` middleware. Customize in config:

```php
'middleware' => ['web', 'auth', 'admin'],
```

## Customizing Views

Publish views to customize the UI:

```bash
php artisan vendor:publish --tag=aicms-views
```

Views will be copied to `resources/views/vendor/aicms/`.

## Security

- Only files matching `editable_paths` patterns can be read/modified
- Uses your existing Laravel authentication
- API key is stored server-side, never exposed to client

## License

MIT
