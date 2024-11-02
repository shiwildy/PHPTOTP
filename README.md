# PHPTOTP
A simple TOTP library for PHP

## Installation
```bash
composer require shiwildy/phptotp
```

## Example
```php
<?php
    require 'vendor/autoload.php';
    use ShiWildy\phptotp;

    // create phptotp instance
    $phptotp = new phptotp();

    // Generate Secret Key
    echo $totp->getSecret();
?>
```

## Contributing
Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License
This project licensed under The MIT License
