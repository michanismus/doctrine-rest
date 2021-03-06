<?php namespace Pz\Doctrine\Rest\BuilderChain;

use Pz\Doctrine\Rest\BuilderChain\Exceptions\InvalidChainMember;
use Pz\Doctrine\Rest\BuilderChain\Exceptions\InvalidChainMemberResponse;

class Chain
{
    /**
     * @var array
     */
    protected $members = [];

    /**
     * Provide class or interface for verification member return.
     *
     * @return string|bool
     */
    public function buildClass()
    {
        return false;
    }

    /**
     * @param array $members
     *
     * @return static
     */
    public static function create(array $members = [])
    {
        return new static($members);
    }

    /**
     * QueryBuilderChain constructor.
     *
     * @param array|MemberInterface $members
     */
    public function __construct(array $members = [])
    {
        $this->add($members);
    }

    /**
     * @param array|MemberInterface|callable $member
     *
     * @return $this
     * @throws InvalidChainMember
     */
    public function add($member)
    {
        if (is_array($member)) {
            foreach ($member as $item) {
                $this->add($item);
            }

            return $this;
        }

        if (!is_callable($member)) {
            throw new InvalidChainMember();
        }

        $this->members[] = $member;

        return $this;
    }

    /**
     * @param object $object
     *
     * @return object
     *
     * @throws InvalidChainMember
     * @throws InvalidChainMemberResponse
     */
    public function process($object)
    {
        /** @var callable|MemberInterface $member */
        foreach ($this->members as $member) {
            if (!is_callable($member)) {
                throw new InvalidChainMember();
            }

            $qb = call_user_func($member, $object);

            if (($class = $this->buildClass()) && !($qb instanceof $class)) {
                throw new InvalidChainMemberResponse($class);
            }
        }

        return $object;
    }
}
