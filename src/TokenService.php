<?php


namespace Amot\Conversate;


use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Ramsey\Uuid\Uuid;

class TokenService
{
    public function generateToken($user_id, $ttl = null): string
    {
        $config = $this->getJwtConfig();
        assert($config instanceof Configuration);
        $now = new DateTimeImmutable();

        if (!$ttl) {
            $ttl = config("conversate.jwt.ttl") ?? 30;
        }
        return $config->builder()
            ->issuedBy(config('conversate.jwt.issuer'))
            ->permittedFor('*')
            ->identifiedBy(base64_encode("conversate.token.$user_id"))
            ->issuedAt($now)
            ->permittedFor(config('conversate.jwt.audience'))
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify("+$ttl minutes"))
            ->withClaim('uid', "$user_id")
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }

    private function getJwtConfig(): Configuration
    {
        return Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::base64Encoded(config('conversate.jwt.secret'))
        );
    }

    public function verifyToken(string $jwt)
    {
        try {
            $config = $this->getJwtConfig();
            $config->setValidationConstraints(new IssuedBy(config('conversate.jwt.issuer')));
            $token = $config->parser()->parse($jwt);
            assert($token instanceof UnencryptedToken);
            $constraints = $config->validationConstraints();

            $config->validator()->assert($token, ...$constraints);
            $claims = $token->claims();
            if (!empty($claims)) {
                return $claims->get('uid');
            }
            return null;
        } catch (RequiredConstraintsViolated $e) {
            logger($e->violations());
            return null;
        } catch (\Exception $e) {
            logger($e->getMessage());
            return null;
        }
    }
}
