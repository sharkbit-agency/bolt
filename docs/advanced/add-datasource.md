---
title: Custom Datasource
weight: 6
---

## Create Custom Datasource

Create a custom data source for your forms and make it available as a collection.
Datasources are helpful for creating custom collections from different models or an external API.

to create datasource, use the following command, passing the name of the data source:

```bash
php artisan make:zeus-datasource Car
```

## Caching

Bolt will automatically list the data sources from your app in the form builder as a collection.
There is a cache for all collections, so remember to flush the key `bolt.datasources`

## Customization
check out the contract `LaraZeus\Bolt\DataSources\DataSourceContract` and see all the available methods.

### Disabling

You can turn off any field temporally by adding:
```php
public bool $disabled = true;
```
