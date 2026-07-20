<?php

// Loader Doctrine pour PHPStan (référencé par phpstan.neon: doctrine.objectManagerLoader).
// Boote le Kernel Symfony et retourne l'ObjectManager par défaut.

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
