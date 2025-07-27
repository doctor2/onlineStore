<?php

namespace App\Bundle\CoreBundle\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Firebase\JWT\JWT;

class JwtTokenProvider
{
    private Configuration $config;

    public function __construct(string $mercureSecret)
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($mercureSecret)
        );
    }

    public function generateSubscribeToken(int $userId): string
    {
        $now = new \DateTimeImmutable();

        $token = $this->config->builder()
            ->issuedBy('shop-issuer')
            ->permittedFor('shop-audience')
            ->issuedAt($now)
            ->expiresAt($now->modify('+50 hour'))
            ->withClaim('subscribe', [
                "http://localhost/user/$userId"
            ])
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

}
