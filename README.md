# ApproximateDateBundle

A twig extension bundle for formatting dates with the output of the seasons or month without an exact date for different languages.

## Installation

### Add repository to composer.json

```json
[...]
"require" : {
    [...]
    "metalslave/approximate-date-bundle" : "dev-master"
},
"repositories" : [{
    "type" : "vcs",
    "url" : "https://github.com/metalslave/approximate-date-bundle.git"
}],
[...]
```
### Add dependency via Composer

```php composer.phar require "metalslave/approximateDateBundle"="1.x-dev"```

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

**d** - day (5), 

**MMMM** - full month name (October);
 
**MMM** - short month name (Oct);
 
**MM** - month number (10);
 
**Y** or **y** - year (2018);

**YY** or **yy** - year (18);
 
**HH:mm** - time (13:45)

and [more ...](http://php.net/manual/en/function.date.php)
 
**S** - season (Autumn);
 
At the same time, you can only use either **S** or **MMMM**
```twig
{{ date('now')|met_format_datetime('Y S') }} 
{#2018 Autumn#}
{{ date('now')|met_format_datetime('MMM Y') }} 
{#Oct 2018#}
{{ date('now')|met_format_datetime('MMMM Y', 'uk') }} 
{#жовтень 2018#}
{{ date('now')|met_format_datetime('d MMMM Y HH:mm', 'uk', 'Europe/Kiev') }} 
{#5 жовтня 2018 14:41#}
{{ date('now')|met_format_date_only('d MMMM Y HH:mm', 'uk') }} 
{#5 жовтня 2018#}
{{ date('now')|met_format_time_only('d MMMM Y HH:mm', 'uk') }} 
{#14:41#}
```
### Language extension
Currently, the bands describe the seasons for "uk" and "en" locales, but you can easily expand it.
add _app/Resources/translations/MetalslaveApproximateDateBundle.ru.yml_ for ru example
```yaml
января: '{0}январь|{1}зима'
февраля: '{0}февраль|{1}зима'
марта: '{0}март|{1}весна'
апреля: '{0}апрель|{1}весна'
мая: '{0}май|{1}весна'
июня: '{0}июнь|{1}лето'
июля: '{0}июль|{1}лето'
августа: '{0}август|{1}лето'
сентября: '{0}сентябрь|{1}осень'
октября: '{0}октябрь|{1}осень'
ноября: '{0}ноябрь|{1}осень'
декабря: '{0}декабрь|{1}зима'
```
