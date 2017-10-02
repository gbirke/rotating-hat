<?php
declare( strict_types = 1);

namespace Gbirke\TaskHat;

use DateTime;

class TaskSpec
{
    private $labels;
    private $startDate;
    private $duration;
    private $endDate;

    public function __construct(array $labels, DateTime $startDate, int $duration, ?DateTime $endDate = null )
    {
        $this->labels = $labels;
        $this->startDate = $startDate;
        $this->duration = $duration;
        $this->endDate = $endDate;
    }

    public function getLabels(): array
    {
        return $this->labels;
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