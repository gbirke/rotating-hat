<?php
declare( strict_types = 1);

namespace Gbirke\TaskHat;

use DateTime;

class TaskSpec
{
    private $names;
    private $startDate;
    private $duration;
    private $endDate;
    // TODO prefix

    public function __construct( array $names, DateTime $startDate, int $duration, ?DateTime $endDate = null )
    {
        $this->names = $names;
        $this->startDate = $startDate;
        $this->duration = $duration;
        $this->endDate = $endDate;
    }

    public function getNames(): array
    {
        return $this->names;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }


}