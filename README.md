# PimcoreDataSaveHandler

With this bundle its possible to automate storaging of pimcore data objects.
This means automatic namescheming and folder building.

## Installation

```json
"require" : {
    "lukaschel/pimcore-data-save-handler" : "~1.0.0"
}
```

## Usage
The bundle listens on the pimcore.dataobject.preUpdate Event so it is possible
to build folders and names by data object fields.

```yaml
pimcore_data_save_handler:
    custom_save_handling:
        Pimcore\Model\DataObject\TestObject:
            key: '{email}'
            path: '/test/{domain}/'
```

## Copyright and license
For licensing details please visit [LICENSE.md](LICENSE.md)
