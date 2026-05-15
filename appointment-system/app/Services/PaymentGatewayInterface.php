<?php

declare(strict_types=1);

namespace App\Services;

interface PaymentGatewayInterface
{
  public function createPayment(array $payment, array $customer, array $meta = []): array;

  public function verifyPayment(array $payload): array;

  public function refundPayment(array $payment, float $amount): array;

  public function handleCallback(array $payload): array;
}
