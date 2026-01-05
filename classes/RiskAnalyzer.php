<?php

abstract class Analyzer {
    abstract public function calculateScore();
    abstract public function getResult();
}

class RiskAnalyzer extends Analyzer {

    private int $smoking;
    private int $sleep;
    private float $bmi;
    private int $age;
    private int $activity;

    private int $score = 0;

    public function __construct($smoking, $sleep, $bmi, $age, $activity) {
        $this->smoking  = $smoking;
        $this->sleep    = $sleep;
        $this->bmi      = $bmi;
        $this->age      = $age;
        $this->activity = $activity;
    }

    // Encapsulation
    private function addPoints(int $points): void {
        $this->score += $points;
    }

    // Abstract method
    public function calculateScore(): void {

        if ($this->smoking === 1) $this->addPoints(3);
        if ($this->sleep < 6)     $this->addPoints(1);

        if ($this->bmi >= 30)      $this->addPoints(2);
        elseif ($this->bmi >= 25)  $this->addPoints(1);

        if ($this->age >= 60)      $this->addPoints(2);
        elseif ($this->age >= 45)  $this->addPoints(1);

        if ($this->activity === 0) $this->addPoints(1);
    }

    // Abstract method
    public function getResult(): array {

        if ($this->score <= 2) {
            return ["Düşük Risk", "success", 30];
        } elseif ($this->score <= 5) {
            return ["Orta Risk", "warning", 60];
        } else {
            return ["Yüksek Risk", "danger", 90];
        }
    }
}
