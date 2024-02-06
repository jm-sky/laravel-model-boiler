# Laravel Model Boiler

> :warning: **This package is Work In Progress (WIP)**

This package is aimed to help developers to quickly generate PHP & TypeScript models, interfaces & classes.



## Installation
- Install package
  
  `composer require dev-made-it/laravel-model-boiler`

- Publish config file
  
   `php artisan vendor:publish --tag=boiler-config`

## Usage
Run `php artisan boiler:all MODEL_NAME`, i.e. `php artisan boiler:all User`

## Configuration


## Roadmap
- [ ] Generate PHP annotations for Laravel model 
- [ ] Generate PHP relations for Laravel model 
- [ ] Generate Laravel resources & resource collections
- [ ] Generate resource REST API controllers
- [ ] Generate TypeScript from database migrations
  - [x] interface
  - [x] model
  - [ ] enums
  - [ ] custom types
