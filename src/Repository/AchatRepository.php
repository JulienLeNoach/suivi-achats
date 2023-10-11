<?php

namespace App\Repository;

use App\Entity\Achat;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Achat>
 *
 * @method Achat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achat[]    findAll()
 * @method Achat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry,Security $security)
    {
        parent::__construct($registry, Achat::class);
        $this->security = $security;

    }

    //getCountsByDateAndType est une méthode qui récupère et compte les données
    //dans une base de données selon le mois de la date de saisie,
    //tout en regroupant les résultats par mois. Elle prend en compte plusieurs
    //filtres comme le type de marché, l'état d'achat, et des informations d'utilisateur
    //comme le numéro SIRET, le code UO, le code CPV et le code de formation.
    //prepareCountsArray est une méthode qui transforme le résultat en un tableau avec
    //le nombre d'éléments et la somme totale pour chaque mois de l'année.
    public function searchAchat($form, PaginatorInterface $paginator)
    {
        $data = $form->getData();
        $queryBuilder = $this->createQueryBuilder('b');
        $achatmin = $form["montant_achat_min"]->getData();
        $user = $this->security->getUser();    
        $queryBuilder
            ->select('b')
            ->Where('b.utilisateurs = :utilisateurs')
            ->setParameter('utilisateurs', $user)
            ->andWhere('b.etat_achat IN (:etat_achats)')
            ->setParameter('etat_achats', [0, 2]);
            if ($form["objet_achat"]->getData()){
                $queryBuilder
                    ->andWhere('b.objet_achat LIKE :objet_achat')
                    ->setParameter('objet_achat', '%' . $data->getObjetAchat() . '%');
            }

            if ($form["num_siret"]->getData()) {
                $queryBuilder
                    ->andWhere('b.num_siret = :num_siret')
                    ->setParameter('num_siret', $data->getNumSiret());
            }
            if ($form["utilisateurs"]->getData()) {
                $queryBuilder 
                    ->andWhere('b.utilisateurs = :utilisateurs')
                    ->setParameter('utilisateurs', $data->getUtilisateurs());
            }
            if ($form["code_uo"]->getData()) {
                $queryBuilder
                    ->andWhere('b.code_uo = :code_uo')
                    ->setParameter('code_uo', $data->getCodeUo());
            }
            if ($form["code_cpv"]->getData()) {
                $queryBuilder
                    ->andWhere('b.code_cpv = :code_cpv')
                    ->setParameter('code_cpv', $data->getCodeCpv());
            }

            if ($form["code_formation"]->getData()) {
                $queryBuilder
                    ->andWhere('b.code_formation = :code_formation')
                    ->setParameter('code_formation', $data->getCodeFormation());
            }
            if ($form["etat_achat"]->getData()) {
                $queryBuilder
                    ->andWhere('b.etat_achat = :etat_achat')
                    ->setParameter('etat_achat', $data->getEtatAchat());
            }
            if ($form["devis"]->getData()) {
                $queryBuilder
                    ->andWhere('b.devis = :devis')
                    ->setParameter('devis', $data->getDevis());
            }
            if ($form["type_marche"]->getData()) {
                $queryBuilder
                    ->andWhere('b.type_marche = :type_marche')
                    ->setParameter('type_marche', $data->getTypeMarche());
            }
            if ($form["montant_achat"]->getData()) {
                $queryBuilder
                    ->andWhere('b.montant_achat < :montant_achat2')
                    ->andWhere('b.montant_achat > :achatmin')
                    ->setParameter('achatmin', $achatmin)
                    ->setParameter('montant_achat2', $data->getMontantAchat());
            }
            if ($form["date"]->getData()) {
                $queryBuilder
                    ->andWhere('b.date_saisie LIKE :date_saisie')
                    ->setParameter('date_saisie', '%' . $form["date"]->getData() . '%');
            }
        // ... Votre logique de construction de la requête ici ...
    
        $query = $queryBuilder->getQuery();
    
        return $query;
    }
    
    public function getPurchaseCountAndTotalAmount($type,$form)
    {   
        $data = $form->getData();
        $date = $form["date"]->getData();
        $tax = $form["tax"]->getData();
        $qb = $this->createQueryBuilder('a');
        if($tax=='ht'){
            $result = $qb->select('MONTH(a.date_saisie) AS month, COUNT(a) AS count,SUM(a.montant_achat) AS totalmontant')
                ->andWhere('a.date_saisie LIKE :date_saisie')
                ->andWhere('a.type_marche = :type_marche')
                ->andWhere('a.etat_achat IN (:etat_achats)')
                ->setParameter('date_saisie', '%' . $date . '%')
                ->setParameter('type_marche', $type)
                ->setParameter('etat_achats', [0, 2]);
        }
        elseif($tax=='ttc'){
            $result = $qb->select('MONTH(a.date_saisie) AS month, COUNT(a) AS count,SUM(a.montant_achat * (1 + t.tva_taux / 100)) AS totalmontant')
                ->innerJoin('\App\Entity\TVA', 't', Join::WITH, 'a.tva_ident = t.id') 
                ->andWhere('a.date_saisie LIKE :date_saisie')
                ->andWhere('a.type_marche = :type_marche')
                ->andWhere('a.etat_achat IN (:etat_achats)')
                ->setParameter('date_saisie', '%' . $date . '%')
                ->setParameter('type_marche', $type)
                ->setParameter('etat_achats', [0, 2]);
        }

        if ($data->getUtilisateurs()) {
            $qb->andWhere('a.utilisateurs = :utilisateurs')
                ->setParameter('utilisateurs', $data->getUtilisateurs());
        }
        if ($data->getNumSiret()) {
            $qb
                ->andWhere('a.num_siret = :num_siret')
                ->setParameter('num_siret', $data->getNumSiret());
        }if ($data->getCodeUo()) {
            $qb
                ->andWhere('a.code_uo = :code_uo')
                ->setParameter('code_uo', $data->getCodeUo());
        }
        if ($data->getCodeCpv()) {
            $qb
                ->andWhere('a.code_cpv = :code_cpv')
                ->setParameter('code_cpv', $data->getCodeCpv());
        }

        if ($data->getCodeFormation()) {
            $qb
                ->andWhere('a.code_formation = :code_formation')
                ->setParameter('code_formation', $data->getCodeFormation());
        }
        $result = $qb->groupBy('month')
            ->getQuery()
            ->getResult();

            
             


        return $result;
    }
   


    public function edit(Achat $achat, bool $flush = false): void
    {
        $this->getEntityManager()->persist($achat);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Achat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Find/search articles by title/content
    public function findArticlesByName(string $query)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('p.objet_achat', ':query'),
                    ),
                    /*                     $qb->expr()->isNotNull('p.dateSaisie')
 */
                )
            )
            ->setParameter('query', '%' . $query . '%');
        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findArticlesByTri(string $query2)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('p.montant_achat', ':query'),
                    ),
                    /*                     $qb->expr()->isNotNull('p.dateSaisie')
 */
                )
            )
            ->setParameter('query', '%' . $query2 . '%');
        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findAllPublishedOrderedByRecentlyActive(string $query3)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.code_formation', 't')
            ->andWhere('t.libelle_formation = :query3')
            ->setParameter('query3', $query3)
            ->getQuery()
            ->getResult();
    }



}
