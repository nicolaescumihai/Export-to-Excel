# Export-to-Excel
Algorithm to export to Excel all columns properties for all tables and all extended properties in a MS SQL database

In order for this to work you will have to instal the packeage from Laravel Excel.
Require this package in the composer.json of your Laravel project. This will download the package and PhpSpreadsheet.

composer require maatwebsite/excel

The Maatwebsite\Excel\ExcelServiceProvider is auto-discovered and registered by default.

If you want to register it yourself, add the ServiceProvider in config/app.php:

'providers' => [
    /*
     * Package Service Providers...
     */
    Maatwebsite\Excel\ExcelServiceProvider::class,
]
The Excel facade is also auto-discovered.

If you want to add it manually, add the Facade in config/app.php:

'aliases' => [
    ...
    'Excel' => Maatwebsite\Excel\Facades\Excel::class,
]
To publish the config, run the vendor publish command:

php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
This will create a new config file named config/excel.php.
