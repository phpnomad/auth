<?php

namespace PHPNomad\Auth\Builders;

class JwtPayloadBuilder
{
    protected array $payload = [];

    /**
     * Sets the 'issuer' claim, which identifies the principal that issued the JWT.
     * @param string $issuer The issuer of the JWT.
     * @return $this Instance for method chaining.
     */
    public function setIssuer($issuer)
    {
        $this->payload['iss'] = $issuer;
        return $this;
    }

    /**
     * Sets the 'subject' claim, which identifies the principal that is the subject of the JWT.
     * @param string $subject The subject of the JWT.
     * @return $this Instance for method chaining.
     */
    public function setSubject($subject)
    {
        $this->payload['sub'] = $subject;
        return $this;
    }

    /**
     * Sets the 'audience' claim, which identifies the recipients that the JWT is intended for.
     * @param mixed $audience The audience of the JWT.
     * @return $this Instance for method chaining.
     */
    public function setAudience($audience)
    {
        $this->payload['aud'] = $audience;
        return $this;
    }

    /**
     * Sets the 'expiration time' claim, which identifies the expiration time on or after which the JWT must not be accepted for processing.
     * @param int $time The expiration time (UNIX timestamp).
     * @return $this Instance for method chaining.
     */
    public function setExpirationTime($time)
    {
        $this->payload['exp'] = $time;
        return $this;
    }

    /**
     * Sets the 'not before' claim, which identifies the time before which the JWT must not be accepted for processing.
     * @param int $time The time before which the JWT is not valid (UNIX timestamp).
     * @return $this Instance for method chaining.
     */
    public function setNotBefore($time)
    {
        $this->payload['nbf'] = $time;
        return $this;
    }

    /**
     * Sets the 'issued at' claim, which identifies the time at which the JWT was issued.
     * @param int $time The issue time of the JWT (UNIX timestamp).
     * @return $this Instance for method chaining.
     */
    public function setIssuedAt($time)
    {
        $this->payload['iat'] = $time;
        return $this;
    }

    /**
     * Sets the 'JWT ID' claim, which provides a unique identifier for the JWT.
     * @param string $jti The unique identifier for the JWT.
     * @return $this Instance for method chaining.
     */
    public function setJWTID($jti)
    {
        $this->payload['jti'] = $jti;
        return $this;
    }

    /**
     * Sets a generic value to the payload.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function set(string $key, string $value)
    {
        $this->payload[$key] = $value;

        return $this;
    }

    /**
     * Returns the fully assembled payload, ready for JWT encoding.
     * @return array The constructed payload.
     */
    public function build(): array
    {
        return $this->payload;
    }
}
