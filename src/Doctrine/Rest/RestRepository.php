<?php namespace Pz\Doctrine\Rest;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

class RestRepository extends EntityRepository
{
    /**
     * @var string
     */
    protected $rootAlias;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function em()
    {
        return $this->getEntityManager();
    }

    /**
     * Base root alias for queries.
     *
     * @return string
     */
    public function alias()
    {
        if ($this->rootAlias === null) {
            $reflectionClass = $this->getClassMetadata()->getReflectionClass();
            if ($reflectionClass->implementsInterface(JsonApiResource::class)) {
                $this->rootAlias = call_user_func($reflectionClass->getName(). '::getResourceKey');
            } else {
                // Camel case to underscore-case
                $this->rootAlias = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $reflectionClass->getShortName()));
            }
        }

        return $this->rootAlias;
    }

    /**
     * @param RestRequestContract $request
     *
     * @return null|object
     * @throws EntityNotFoundException
     */
    public function findByIdentifier(RestRequestContract $request)
    {
        if (null === ($entity = $this->find($request->getId()))) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                $this->getClassName(),
                ['id' => $request->getId()]
            );
        }

        return $entity;
    }
}
