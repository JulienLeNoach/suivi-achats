<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AchatCounter;
use App\Entity\Achat;

class AchatNumberService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateAchatNumber(): string
    {
        $currentYear = (int) date('Y');
        $achatCounter = $this->entityManager->getRepository(AchatCounter::class)->findOneByYear($currentYear);

        if (!$achatCounter) {
            $achatCounter = new AchatCounter();
            $achatCounter->setYear($currentYear);
            $achatCounter->setLastNumber(1);
            $this->entityManager->persist($achatCounter);
        } else {
            $lastNumber = $achatCounter->getLastNumber() + 1;
            $achatCounter->setLastNumber($lastNumber);
        }

        $this->entityManager->flush();

        return sprintf("%d-%04d", $currentYear, $achatCounter->getLastNumber());
    }
}
