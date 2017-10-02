<?php

namespace Gbirke\TaskHat;

class Recurrence
{
    const ONCE = 1;
    const UNTIL = 2;
    const FOREVER = 3;

    private $endDate;
    private $type;

    private function __construct( int $type, ?\DateTime $endDate = null )
    {
        $this->endDate = $endDate;
        $this->type = $type;
    }

    public static function newOnce(): self {
        return new self( self::ONCE );
    }

    public static function newUntil( \DateTime $until ): self {
        return new self( self::UNTIL, $until );
    }

    public static function newForever(): self {
        return new self( self::FOREVER );
    }

    public function isRecurring(): bool {
        return $this->type !== self::ONCE;
    }

    public function isForever()
    {
        return $this->type === self::FOREVER;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }
}