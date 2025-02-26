<?php

namespace App\Tests\Entity;

use App\Entity\CPV;
use PHPUnit\Framework\TestCase;

class CPVTest extends TestCase
{
    public function testCreateCPV()
    {
        $cpv = new CPV();

        $this->assertInstanceOf(CPV::class, $cpv);
        $this->assertEquals(30000, $cpv->getPremierSeuil());
    }

    public function testSetAndGetCodeCpv()
    {
        $cpv = new CPV();
        $cpv->setCodeCpv("ABC123");

        $this->assertEquals("ABC123", $cpv->getCodeCpv());
    }

    public function testSetAndGetLibelleCpv()
    {
        $cpv = new CPV();
        $cpv->setLibelleCpv("Libellé test");

        $this->assertEquals("Libellé test", $cpv->getLibelleCpv());
    }

    public function testSetAndGetEtatCpv()
    {
        $cpv = new CPV();
        $cpv->setEtatCpv(true);

        $this->assertTrue($cpv->getEtatCpv());
    }

    public function testSetAndGetMtCpvAuto()
    {
        $cpv = new CPV();
        $cpv->setMtCpvAuto(15000.50);

        $this->assertEquals(15000.50, $cpv->getMtCpvAuto());
    }

    public function testSetAndGetPremierSeuil()
    {
        $cpv = new CPV();
        $cpv->setPremierSeuil(25000);

        $this->assertEquals(25000, $cpv->getPremierSeuil());
    }

    public function testToString()
    {
        $cpv = new CPV();
        $cpv->setCodeCpv("123456");
        $cpv->setLibelleCpv("Exemple CPV");

        $this->assertEquals("123456 - Exemple CPV", (string) $cpv);
    }
}
