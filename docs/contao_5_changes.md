# Contao 5 changes not in the fucking doc


## WEM\SmartgearBundle\DataContainer

### `extends`

Just extends `Backend` if wee need some of its functions, otherwise it could not extends anything IMO

### `checkPermission`

Now it's in `core-bundle/contao/classes/DataContainer::denyAccessUnlessGranted` and is checked before actual deletion/edit/...
Keeping our `checkPermission` function which throws AccessDeniedException or void is OK !

### `deleteItem`

We can keep our callback but we have to modify it
New template :
```php
public function deleteItem(Contao\CoreBundle\DataContainer\DataContainerOperation &$config): void
{
	if (!$this->canItemBeDeleted((int) $config->getRecord()['id'])) {
        $config->disable();
    }
}
```

See `core-bundle/contao/classes/DataContainer::generateButtons` creating `core-bundle/src/DataContainer/DataContainerOperation` and passing it in the callback.

Our old function signature would still work, but it would be our job to generate the full HTML ... (or re-creating a `DataContainerOperation` from scratch ...).


## WEM\SmartgearBundle\DataContainer\StyleManagerArchive

`tl_style_manager_archive` does not exist anymore.
Use `Oveleon\ContaoComponentStyleManager\EventListener\DataContainer\StyleManagerArchiveListener` instead (needs a `RouterInterface` in constructor but bundle is in `autowire`, so maybe it works out of the box ?).

## WEM\SmartgearBundle\DataContainer\StyleManager

`tl_style_manager` does not exist anymore.
Use `Oveleon\ContaoComponentStyleManager\EventListener\DataContainer\StyleManagerListener` instead.