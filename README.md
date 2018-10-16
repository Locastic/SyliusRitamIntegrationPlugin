
<h1 align="center">Ritam integration Sylius plugin</h1>

## Setup

Add the following line to your config.yml:

    
    - { resource: "@LocasticSyliusRitamIntegrationPlugin/Resources/config/config.yml" }
 
Add the following lines to your routing.yml file:
    
    locastic_sylius_routing:
        resource: "@LocasticSyliusRitamIntegrationPlugin/Resources/config/routing.yml"
        prefix: /admin

Add parameters to parameters.yml or .env:
    
    parameters:
        ritam_api_host: "%env(RITAM_API_HOST)%"
        ritam_api_version: "%env(RITAM_API_VERSION)%"
        ritam_api_secret: "%env(RITAM_API_SECRET)%"
        ritam_api_username: "%env(RITAM_API_USERNAME)%"
        default_import_locale: "%env(DEFAULT_IMPORT_LOCALE)%"
        default_import_channel: "%env(DEFAULT_IMPORT_CHANNEL)%"
        default_import_taxon: "%env(DEFAULT_IMPORT_TAXON)%"
    
        env(RITAM_API_HOST): 'http://webservice.ritam.hr:8920'
        env(RITAM_API_VERSION): '/rest/api/v1'
        env(RITAM_API_SECRET): 'password'
        env(RITAM_API_USERNAME): 'username'
        env(DEFAULT_IMPORT_LOCALE): 'en_US'
        env(DEFAULT_IMPORT_CHANNEL): 'US_WEB'
        env(DEFAULT_IMPORT_TAXON): 'category'

    
Require SyliusRitamIntegrationPlugin in AppKernel.php
    
     new \Locastic\SyliusRitamIntegrationPlugin\LocasticSyliusRitamIntegrationPlugin(),

Add **state_machine.yml** file to app/config folder with this content:
     
     
     winzou_state_machine:
         sylius_order_checkout:
             callbacks:
                 after:
                     sylius_save_checkout_completion_date:
                         on: ["complete"]
                         do: ["@locatic_sylius_ritam_integration_plugin.order_sender", "sendSyliusOrderToRitamApi"]
                         args: ["object"]

And add this line to your app/config.yml:


      - { resource: "state_machine.yml" }
      
Update your database schema via migrations or update schema command.

You should now be able to import products, prices and stock information via following commands:

        php bin/console locastic:sylius:import-ritam-products
        php bin/console locastic:sylius:import-ritam-prices
        php bin/console locastic:sylius:import-ritam-stock
       
Import can also be triggered on these routes:
    
        (prefix)/import-ritam-products
        (prefix)/import-ritam-prices
        (prefix)/import-ritam-stock
     

