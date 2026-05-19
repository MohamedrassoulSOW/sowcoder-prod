<?php

require dirname(__DIR__).'/vendor/autoload.php';

$kernel = new App\Kernel('dev', true);
$kernel->boot();
$c = $kernel->getContainer();

$repo = $c->get(App\Repository\UserRepository::class);
$hasher = $c->get('security.user_password_hasher');
$jwt = $c->get(Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface::class);

$user = $repo->findByEmail('admin@sowcoder.sn');
echo 'user: '.($user ? 'yes' : 'no').PHP_EOL;

if ($user) {
    echo 'valid: '.($hasher->isPasswordValid($user, 'ChangezMoi123!') ? 'yes' : 'no').PHP_EOL;
    try {
        $token = $jwt->create($user);
        echo 'token ok: '.substr($token, 0, 30).'...'.PHP_EOL;
    } catch (Throwable $e) {
        echo 'jwt err: '.$e->getMessage().PHP_EOL;
        echo $e->getTraceAsString().PHP_EOL;
    }
}
