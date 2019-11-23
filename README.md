# Nova Json Wrapper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digital-creative/nova-json-wrapper)](https://packagist.org/packages/digital-creative/nova-json-wrapper)
[![Total Downloads](https://img.shields.io/packagist/dt/digital-creative/nova-json-wrapper)](https://packagist.org/packages/digital-creative/nova-json-wrapper)
[![License](https://img.shields.io/packagist/l/digital-creative/nova-json-wrapper)](https://github.com/dcasia/nova-json-wrapper/blob/master/LICENSE)

This field allows you to group Nova fields and merge their output into a single JSON column.

# Installation

You can install the package via composer:

```
composer require digital-creative/nova-json-wrapper
```

## Usage

Firstly you will need to update your model to cast the value of your attribute to an array:

```php
class User extends Model
{
    protected $casts = [
        'options' => 'array'
    ];
}
```

Then create a `JsonWrapper` field within your nova resource and use the `HasJsonWrapper` trait.

```php

use DigitalCreative/JsonWrapper/JsonWrapper;
use DigitalCreative/JsonWrapper/HasJsonWrapper;

class User extends Resource
{
    use HasJsonWrapper; // Important!

    public function fields(Request $request)
    {
        //...
        JsonWrapper::make('options', [
        
            Text::make('First Name')->rules('required'),
            Text::make('Last Name')->rules('required'),
        
            JsonWrapper::make('body_mass', [
        
                Text::make('Weight')->rules('required'),
                Text::make('Height')->rules('required'),
               
            ])
        
        ])
    }

}

```

This converts to

```json
{ "first_name": "John", "last_name": "Doe", "body_mass": { "weight": 70, "height": 180 } }
```

and saves to the `options` column on the database.

## Notes

There are no visual indications that the field is wrapped within a json, this is intentional. It was designed to work 
in condition with [Conditional Container](https://github.com/dcasia/conditional-container) allowing to seamlessly
create complex data structure and having it all saved in a single json column into your database, here is an full example:

```php
public function fields(Request $request)
{
    Select::make('Type')
          ->options([
              1, 2, 3, 4, 5
          ])
          ->rules('required'),
    
    ConditionalContainer::make([
    
        JsonWrapper::make('data', [
    
            Text::make('First Name')->rules('required'),
            Text::make('Last Name')->rules('required'),
    
            Select::make('Gender')
                  ->options([
                      'male' => 'Male',
                      'female' => 'Female'
                  ])
                  ->rules('required'),
    
            ConditionalContainer::make([ JsonWrapper::make('extra', [ ... ]) ])->if('gender === male'),
            ConditionalContainer::make([ JsonWrapper::make('extra', [ ... ]) ])->if('gender === female'),
    
        ])
    
    ])->if('type >= 2'),
}
```

## License

The MIT License (MIT). Please see [License File](https://raw.githubusercontent.com/dcasia/nova-json-wrapper/master/LICENSE) for more information.
