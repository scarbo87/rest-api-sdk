<?php

namespace scarbo87\RestApiSdk;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache as DoctrineCache;
use Doctrine\Instantiator\Instantiator as DoctrineInstantiator;
use scarbo87\RestApiSdk\Exception\DecodingException;
use scarbo87\RestApiSdk\Operator\Exception\UnexpectedFileArgumentException;
use scarbo87\RestApiSdk\Mapper\Mapper;
use scarbo87\RestApiSdk\Mapper\Type as t;
use Psr\Http\Message\StreamInterface;

/**
 * @param bool $debug
 *
 * @return Mapper
 */
function simpleMapper($debug = false)
{
    $annotationReader = new CachedReader(new AnnotationReader(), new DoctrineCache\ArrayCache(), $debug);

    $mapper = new Mapper(
        [
            'array' => new t\ArrayType(),
            'scalar' => new t\ScalarType(),
            'date' => new t\DateTimeType(),
            'enum' => new t\EnumType(),
            'object' => new t\ObjectType($annotationReader, new DoctrineInstantiator()),
            'mixed' => new t\MixedType(),
            'dict' => new t\DictType(),
        ]
    );

    return $mapper;
}

/**
 * @param string $json
 *
 * @return mixed
 * @throws DecodingException
 */
function json_decode($json)
{
    if ('' === $json) {
        return null;
    }

    $decoded = @\json_decode($json, true);
    if (null === $decoded && null !== ($error = json_last_error_msg())) {
        throw new DecodingException($error);
    }

    return $decoded;
}

/**
 * @param resource|string|StreamInterface $file
 *
 * @return resource|StreamInterface
 */
function streamOrResource($file)
{
    if (is_string($file)) { // assume it's a file path
        $file = fopen($file, 'r');
    }
    if (!$file instanceof StreamInterface && !is_resource($file)) {
        throw new UnexpectedFileArgumentException($file);
    }

    return $file;
}

/**
 * @param \DateTimeInterface|null $dt
 *
 * @return \DateTimeImmutable|null
 */
function date_immutable(\DateTimeInterface $dt = null)
{
    if ((null === $dt) || ($dt instanceof \DateTimeImmutable)) {
        return $dt;
    }

    $immutable = \DateTimeImmutable::createFromFormat(\DateTime::ISO8601, $dt->format(\DateTime::ISO8601));
    $immutable = $immutable->setTimezone($dt->getTimezone());

    return $immutable;
}

/**
 * @param \DateTimeInterface|null $dt
 *
 * @return \DateTime|null
 */
function date_mutable(\DateTimeInterface $dt = null)
{
    if ((null === $dt) || ($dt instanceof \DateTime)) {
        return $dt;
    }

    $mutable = \DateTime::createFromFormat(\DateTime::ISO8601, $dt->format(\DateTime::ISO8601));
    $mutable->setTimezone($dt->getTimezone());

    return $mutable;
}
