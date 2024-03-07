# adobe-cloud-ece-tools-extend
Download the package using composer 
````
composer require adobe/adobe-maintenance-notification-tools: "dev-main"
````
To execute extended deploy scenario run next command from your Magento Cloud project:
```
php ./vendor/bin/ece-tools run scenario/deploy.xml ./vendor/adobe/adobe-maintenance-notification-tools/scenario/extend-deploy.xml
```

You can extend base scenario with different custom scenarios.
```
php vendor/bin/ece-tools run "path/to/base/scenarion" "path/to/extended/scenarion" "path/to/extended/scenarion2" "path/to/extended/scenarion3"
```
Keep in mind that scenarios will be merged in provided order.

## Set value from Cloud

Into Magento Cloud project configuration variables add
```
# Environment variables
variables:
    env:
        APPLICATION_FRONTEND_URL: 'https://example.com/'
```