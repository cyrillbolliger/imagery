# How to Add a Base Logo

## The 100 simple steps ;)
* Generate a dark and a light SVG of the logo and store it in 
  `storage/app/base_logos/<logo-name>-<light|dark>.svg`
* Create a logo class that implements `App\Logo\LogoCompositor` in `app/Logo`.
  Name the class after your logo.
* Register the logo class in the `app/Logo/LogoFactory.php`
* Add the logo settings to the `LogoTypes` and `LogoSublineRatios` constants in
  `resources/js/service/canvas/Constants.js`
* Add the logo settings to the `LogoTypeRatios` in
  `resources/js/service/canvas/elements/Logo.js`
* Add the logo type to the `types` property of `data()` in
  `js/components/molecules/MLogoForm.vue`


## Todo
Refactor the logo handling, so adding a logo isn't such a pain anymore.
