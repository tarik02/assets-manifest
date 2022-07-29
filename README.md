# assets-manifest

## Installation

```bash
$ composer require tarik02/assets-manifest
```

## Usage

Should be used with [webpack-assets-manifest](https://www.npmjs.com/package/webpack-assets-manifest) plugin.

Recommended plugin configuration:
```js
new WebpackAssetsManifest({
    output: 'manifest.json',
    contextRelativeKeys: true,
    transform: ({ entrypoints, ...assets }) => ({ mode, entrypoints, assets }),
    writeToDisk: false,
    fileExtRegex: /\.\w{2,4}\.(?:map|gz)$|\.\w+$/i,
    publicPath: '',
    entrypoints: true
})
```

```php
use Tarik02\AssetsManifest\ManifestLoader;

$assets = ManifestLoader::from('/path/to/project/public/assets')
    ->load('https://example.test/public/assets')
);
```

Example for Laravel Framework (add this code to `AppServiceProvider::boot`):
```php
$this->app->singleton(
    'assets',
    fn () => ManifestLoader::from(public_path('assets'))
        ->load(url('assets'))
);

// ...

app('assets')->url('img/image.png'); // http://example.test/assets/img/image.deadbeef.png

app('assets')->path('img/image.png'); // /path/to/project/public/assets/img/image.deadbeef.png


$entrypoint = app('assets')->entrypoints->include('app', 'second-page');

$entrypoint->jsCode(); // <script src="..."></script><script src="..."></script>
$entrypoint->cssCode(); // <link rel="stylesheet" href="..."><link rel="stylesheet" href="...">
```
