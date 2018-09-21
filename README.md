
<h1 align="center">Ritam integration Sylius plugin</h1>

## Setup

Add the following line to your config.yml:

    
    - { resource: "@LocasticSyliusRitamIntegrationPlugin/Resources/config/config.yml" }
    
Add parameters to parameters.yml or .env:
    
    parameters:
        ritam_api_host: "%env(RITAM_API_HOST)%"
        ritam_api_version: "%env(RITAM_API_VERSION)%"
        ritam_api_secret: "%env(RITAM_API_SECRET)%"
        ritam_api_username: "%env(RITAM_API_USERNAME)%"
        default_import_locale: "%env(DEFAULT_IMPORT_LOCALE)%"
    
        
        env(RITAM_API_HOST): 'http://webservice.ritam.hr:8920'
        env(RITAM_API_VERSION): '/rest/api/v1'
        env(RITAM_API_SECRET): '~'
        env(RITAM_API_USERNAME): '~'
        env(DEFAULT_IMPORT_LOCALE): 'hr_HR'

    
Require SyliusRitamIntegrationPlugin in AppKernel.php
    
     new \Locastic\SyliusRitamIntegrationPlugin\LocasticSyliusRitamIntegrationPlugin(),

Update your database schema.



<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>


<h1 align="center">Plugin Skeleton</h1>

<p align="center">Skeleton for starting Sylius plugins.</p>

## Installation

1. Run `composer create-project sylius/plugin-skeleton ProjectName`.

2. From the plugin skeleton root directory, run the following commands:

    ```bash
    $ (cd tests/Application && yarn install)
    $ (cd tests/Application && yarn run gulp)
    $ (cd tests/Application && bin/console assets:install web -e test)
    
    $ (cd tests/Application && bin/console doctrine:database:create -e test)
    $ (cd tests/Application && bin/console doctrine:schema:create -e test)
    ```

## Usage

### Running plugin tests

  - PHPUnit

    ```bash
    $ bin/phpunit
    ```

  - PHPSpec

    ```bash
    $ bin/phpspec run
    ```

  - Behat (non-JS scenarios)

    ```bash
    $ bin/behat --tags="~@javascript"
    ```

  - Behat (JS scenarios)
 
    1. Download [Chromedriver](https://sites.google.com/a/chromium.org/chromedriver/)
    
    2. Run Selenium server with previously downloaded Chromedriver:
    
        ```bash
        $ bin/selenium-server-standalone -Dwebdriver.chrome.driver=chromedriver
        ```
    3. Run test application's webserver on `localhost:8080`:
    
        ```bash
        $ (cd tests/Application && bin/console server:run 127.0.0.1:8080 -d web -e test)
        ```
    
    4. Run Behat:
    
        ```bash
        $ bin/behat --tags="@javascript"
        ```

### Opening Sylius with your plugin

- Using `test` environment:

    ```bash
    $ (cd tests/Application && bin/console sylius:fixtures:load -e test)
    $ (cd tests/Application && bin/console server:run -d web -e test)
    ```
    
- Using `dev` environment:

    ```bash
    $ (cd tests/Application && bin/console sylius:fixtures:load -e dev)
    $ (cd tests/Application && bin/console server:run -d web -e dev)
    ```
