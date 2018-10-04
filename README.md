# ApproximateDateBundle

A bundle for formatting dates with the output of the seasons or month without an exact date for different languages.

## Installation

### Add repository to composer.json

```php
[...]
"require" : {
    [...]
    "metalslave/approximateDateBundle" : "dev-master"
},
"repositories" : [{
    "type" : "vcs",
    "url" : "https://github.com/metalslave/approximateDateBundle.git"
}],
[...]
```
### Add dependency via Composer

```php composer.phar require metalslave/approximateDateBundle```

### Register the bundles

To start using the bundle, register it in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = [
        // Other bundles...
        
        new Metalslave\ApproximateDateBundle\MetalslaveApproximateDateBundle(),
    ];
}
```


### Usage
Currently, the bands describe the seasons for "uk" and "en" locales, but you can easily expand it. 
Add to config.yml
```php
metalslave_approximate_date:
    month_and_seasons_data_service: AppBundle\Services\AppMonthAndSeasonDataService 
```
and create
 ```php
 <?php
 
 namespace AppBundle\Services;
 
 use Metalslave\ApproximateDateBundle\Services\MonthsAndSeasonsDataService;
 
 /**
  * Class AppMonthAndSeasonDataService.
  */
 class AppMonthAndSeasonDataService extends MonthsAndSeasonsDataService
 {
     private $rusMonthAndSeasons = [
         'ru' =>
             [
                 'января' => ['январь', 'зима'],
                 'февраля' => ['февраль', 'зима'],
                 'марта' => ['март', 'весна'],
                 'апреля' => ['апрель', 'весна'],
                 'мая' => ['май', 'весна'],
                 'июня' => ['июнь', 'лето'],
                 'июля' => ['июль', 'лето'],
                 'августа' => ['август', 'лето'],
                 'сентября' => ['сентябрь', 'осень'],
                 'октября' => ['октябрь', 'осень'],
                 'ноября' => ['ноябрь', 'осень'],
                 'декабря' => ['декабрь', 'зима'],
             ],
     ];
 
     /**
      * @return array
      */
     public function getMonthsAndSeasons()
     {
         return array_merge(parent::getMonthsAndSeasons(), $this->rusMonthAndSeasons);
     }
 }

 ```