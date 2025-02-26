<?php
// tests/Entity/FournisseursTest.php
namespace App\Tests\Entity;

use App\Entity\Fournisseurs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class FournisseursTest extends TestCase
{
    private Fournisseurs $fournisseur;

    protected function setUp(): void
    {
        // Création d'une instance de l'entité Fournisseurs
        $this->fournisseur = new Fournisseurs();
    }

    public function testGettersAndSetters(): void
    {
        // Test des getters et setters pour les propriétés
        $this->fournisseur->setNumSiret('12345678901234');
        $this->fournisseur->setNomFournisseur('Fournisseur Test');
        $this->fournisseur->setCodePostal('75000');
        $this->fournisseur->setPme(true);
        $this->fournisseur->setCodeClient('12345');
        $this->fournisseur->setNumChorusFournisseur('9876543210');
        $this->fournisseur->setTel('0123456789');
        $this->fournisseur->setMail('fournisseur@test.com');
        $this->fournisseur->setEtatFournisseur(true);
        $this->fournisseur->setDateMajFournisseur('2025-02-03');
        $this->fournisseur->setRue('1 Rue Test');
        $this->fournisseur->setVille('Paris');

        // Assertions des valeurs
        $this->assertEquals('12345678901234', $this->fournisseur->getNumSiret());
        $this->assertEquals('Fournisseur Test', $this->fournisseur->getNomFournisseur());
        $this->assertEquals('75000', $this->fournisseur->getCodePostal());
        $this->assertTrue($this->fournisseur->getPme());
        $this->assertEquals('12345', $this->fournisseur->getCodeClient());
        $this->assertEquals('9876543210', $this->fournisseur->getNumChorusFournisseur());
        $this->assertEquals('0123456789', $this->fournisseur->getTel());
        $this->assertEquals('fournisseur@test.com', $this->fournisseur->getMail());
        $this->assertTrue($this->fournisseur->getEtatFournisseur());
        $this->assertEquals('2025-02-03', $this->fournisseur->getDateMajFournisseur());
        $this->assertEquals('1 Rue Test', $this->fournisseur->getRue());
        $this->assertEquals('Paris', $this->fournisseur->getVille());
    }

    public function testValidation(): void
    {
        // Utilisation du validateur Symfony pour vérifier les contraintes de validation
        $validator = Validation::createValidator();
    
        // Création d'une entité Fournisseurs avec des valeurs invalides pour tester les contraintes
        $invalidFournisseur = new Fournisseurs();
        $invalidFournisseur->setNumSiret('1234567890123456');  // SIRET trop long (16 caractères)
        $invalidFournisseur->setNomFournisseur(str_repeat('A', 256));  // Nom trop long (256 caractères)
        $invalidFournisseur->setCodePostal('ABCDE');  // Code postal invalide (contient des lettres)
    
        // Validation des violations
        $violations = $validator->validate($invalidFournisseur);
    
        // Vérifie qu'il y a des violations
        $this->assertGreaterThan(0, count($violations), 'Aucune violation trouvée.');
    
        // Vérifie qu'il y a bien une violation pour chaque champ
        $violationMessages = iterator_to_array($violations);
        
        $this->assertContains('Le numéro SIRET doit avoir un maximum de 14 caractères.', array_map(function($violation) {
            return $violation->getMessage();
        }, $violationMessages));
        
        $this->assertContains('Le nom du fournisseur ne doit pas dépasser 255 caractères.', array_map(function($violation) {
            return $violation->getMessage();
        }, $violationMessages));
        
        $this->assertContains('Le code postal doit contenir uniquement des chiffres.', array_map(function($violation) {
            return $violation->getMessage();
        }, $violationMessages));
    }

    public function testToString(): void
    {
        // Vérification de la méthode __toString()
        $this->fournisseur->setNumSiret('12345678901234');
        $this->fournisseur->setNomFournisseur('Fournisseur Test');

        $this->assertEquals('12345678901234-Fournisseur Test', (string) $this->fournisseur);
    }
}
