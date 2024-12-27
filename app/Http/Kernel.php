protected $routeMiddleware = [
    'auth.api' => \App\Http\Middleware\JwtMiddleware::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'verifikasi' => \App\Http\Middleware\VerificationMiddleware::class,
];
