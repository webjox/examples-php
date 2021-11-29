<?php

require __DIR__ . '/../entity/RevertClass.php';

use PHPUnit\Framework\TestCase;
use entity\RevertClass;

class RevertTest extends TestCase
{
    private $str = "Привет! Давно не виделись.";
    private $expected = "Тевирп! Онвад ен ьсиледив.";
    private $revert;

    public function __construct()
    {
        parent::__construct();
        $this->revert = RevertClass::revertCharacters($this->str);
    }

    public function testRevertEquals()
    {
        $this->assertEquals($this->expected, $this->revert);
    }

    public function testRevertGreaterThanOrEqual()
    {
        $this->assertGreaterThanOrEqual($this->expected, $this->revert);
    }

    public function testRevertLessThanOrEqual()
    {
        $this->assertLessThanOrEqual($this->expected, $this->revert);
    }

    public function testRevertIsString()
    {
        $this->assertIsString($this->revert);
    }
}

