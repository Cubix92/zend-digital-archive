<?php

namespace LogTest\Controller;

use Log\Model\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testInitialValuesAreDefault()
    {
        $log = new Log();

        $this->assertNull($log->getId());
        $this->assertNull($log->getContent());
        $this->assertNull($log->getType());
        $this->assertNull($log->getDate());
    }

    public function testSetsPropertiesCorrectly()
    {
        $now = new \DateTime();

        $log = (new Log())
            ->setId(123)
            ->setContent('Lorem ipsum dolor sit amet...')
            ->setType(5)
            ->setDate($now);

        $this->assertEquals(123, $log->getId());
        $this->assertEquals('Lorem ipsum dolor sit amet...', $log->getContent());
        $this->assertEquals(5, $log->getType());
        $this->assertEquals($now, $log->getDate());
    }
}
