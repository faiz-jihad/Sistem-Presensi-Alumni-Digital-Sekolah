<?php

use App\Services\WhatsAppService;

it('formats a whatsapp recipient number correctly', function () {
    $service = new WhatsAppService();

    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('formatPhoneNumber');
    $method->setAccessible(true);

    expect($method->invoke($service, '081234567890'))->toBe('6281234567890');
    expect($method->invoke($service, '6281234567890'))->toBe('6281234567890');
    expect($method->invoke($service, '+62 812-3456-7890'))->toBe('6281234567890');
});
