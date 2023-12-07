<?php

namespace App\Repository;

use DateTime;
use App\Entity\Achat;
use App\Factory\AchatFactory;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    private $achatFactory;
    private $entityManager;

    public function __construct(ManagerRegistry $registry,Security $security,AchatFactory $achatFactory,EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Achat::class);
        $this->security = $security;
        $this->achatFactory = $achatFactory;
        $this->entityManager = $entityManager;


    }
    public function searchAchatToStat($form)
    {
        $userId = null;
        $numSiretId = null;
        $cpvId = null;
        $uOId = null;
        $formationId = null;

        $date = $form["date"]->getData();
        $user = $form["utilisateurs"]->getData();
        $numSiret = $form["num_siret"]->getData();
        $cpv = $form["code_cpv"]->getData();
        $uO = $form["code_uo"]->getData();
        $formation = $form["code_formation"]->getData();
        if ($user) {
            // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
            $userId = $user->getId();
        }
        if ($numSiret) {
            $numSiretId = $numSiret->getId();
        }
        if ($cpv) {
            $cpvId = $cpv->getId();
        }
        if ($uO) {
            $uOId = $uO->getId();
        }
        if ($formation) {
            $formationId = $formation->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT
            type_marche,
            COUNT(*) AS nombre_achats,
            COUNT(CASE WHEN type_marche = 0 THEN 1 END) AS nombre_achats_type_0,
            COUNT(CASE WHEN type_marche = 1 THEN 1 END) AS nombre_achats_type_1,
            ROUND((COUNT(CASE WHEN type_marche = 0 THEN 1 END) / NULLIF((SELECT COUNT(*) FROM achat WHERE YEAR(date_saisie) = :year AND etat_achat = 2 " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
            " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
            " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
            " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
            " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "), 0)) * 100, 2) AS pourcentage_type_0,
            ROUND((COUNT(CASE WHEN type_marche = 1 THEN 1 END) / NULLIF((SELECT COUNT(*) FROM achat WHERE YEAR(date_saisie) = :year AND etat_achat = 2 " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
            " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
            " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
            " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
            " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "), 0)) * 100, 2) AS pourcentage_type_1,
            ROUND(SUM(CASE WHEN type_marche = 0 THEN montant_achat ELSE 0 END), 2) AS somme_montant_type_0,
            ROUND(AVG(CASE WHEN type_marche = 0 THEN montant_achat ELSE NULL END), 2) AS moyenne_montant_type_0,
            ROUND(SUM(CASE WHEN type_marche = 1 THEN montant_achat ELSE 0 END), 2) AS somme_montant_type_1,
            ROUND(AVG(CASE WHEN type_marche = 1 THEN montant_achat ELSE NULL END), 2) AS moyenne_montant_type_1,
            ROUND((SUM(CASE WHEN type_marche = 0 THEN montant_achat ELSE 0 END) / NULLIF((SELECT SUM(montant_achat) FROM achat WHERE YEAR(date_saisie) = :year AND etat_achat = 2 " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
            " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
            " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
            " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
            " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "), 0)) * 100, 2) AS pourcentage_type_0_total,
            ROUND((SUM(CASE WHEN type_marche = 1 THEN montant_achat ELSE 0 END) / NULLIF((SELECT SUM(montant_achat) FROM achat WHERE YEAR(date_saisie) = :year AND etat_achat = 2 " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
            " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
            " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
            " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
            " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "), 0)) * 100, 2) AS pourcentage_type_1_total,
            (SELECT COUNT(*) FROM achat WHERE YEAR(date_saisie) = :year AND etat_achat = 2 " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
            " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
            " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
            " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
            " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . " ) AS nombre_total_achats
        FROM
            achat
        WHERE
            type_marche IN (0, 1) AND YEAR(date_saisie) = :year AND etat_achat = 2 
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
            " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
            " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
            " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
            " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
        GROUP BY
            type_marche
        LIMIT 0, 100;";
    
    

$stmt = $conn->prepare($sql);
      
                $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
                $achats=$resultSet->fetchAllAssociative();
               
                return $achats;
    }
    public function searchAchatToStatMount($form)
    {
        $userId = null;
        $numSiretId = null;
        $cpvId = null;
        $uOId = null;
        $formationId = null;

        $date = $form["date"]->getData();
        $user = $form["utilisateurs"]->getData();
        $numSiret = $form["num_siret"]->getData();
        $cpv = $form["code_cpv"]->getData();
        $uO = $form["code_uo"]->getData();
        $formation = $form["code_formation"]->getData();
        if ($user) {
            // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
            $userId = $user->getId();
        }
        if ($numSiret) {
            $numSiretId = $numSiret->getId();
        }
        if ($cpv) {
            $cpvId = $cpv->getId();
        }
        if ($uO) {
            $uOId = $uO->getId();
        }
        if ($formation) {
            $formationId = $formation->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT
        type_marche,
        COUNT(CASE WHEN montant_achat <= p.four2 THEN 1 END) AS nombre_achats_inf_four1,
        COUNT(CASE WHEN montant_achat > p.four2 AND montant_achat <= p.four3 THEN 1 END) AS nombre_achats_four1_four2,
        COUNT(CASE WHEN montant_achat > p.four3 AND montant_achat <= p.four4 THEN 1 END) AS nombre_achats_four2_four3,
        COUNT(CASE WHEN montant_achat > p.four4 THEN 1 END) AS nombre_achats_sup_four3,
        COUNT(*) AS nombre_total_achats
    FROM
        achat a
    JOIN
        parametres p ON a.code_service_id = p.code_service_id 
WHERE
    type_marche IN (0, 1) AND YEAR(date_saisie) = :year AND etat_achat = 2 
    " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
    GROUP BY
    type_marche;";
    
    

$stmt = $conn->prepare($sql);
      
                $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
                $achats=$resultSet->fetchAllAssociative();
                return $achats;
    }
    public function statisticPMESum($form)
    {
        $userId = null;
        $numSiretId = null;
        $cpvId = null;
        $uOId = null;
        $formationId = null;

        $date = $form["date"]->getData();
        $user = $form["utilisateurs"]->getData();
        $numSiret = $form["num_siret"]->getData();
        $cpv = $form["code_cpv"]->getData();
        $uO = $form["code_uo"]->getData();
        $formation = $form["code_formation"]->getData();
        if ($user) {
            // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
            $userId = $user->getId();
        }
        if ($numSiret) {
            $numSiretId = $numSiret->getId();
        }
        if ($cpv) {
            $cpvId = $cpv->getId();
        }
        if ($uO) {
            $uOId = $uO->getId();
        }
        if ($formation) {
            $formationId = $formation->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT
            ROUND(COUNT(achat.id), 2) AS VolumePME,
            ROUND(SUM(achat.montant_achat), 2) AS ValeurPME,
            ROUND((COUNT(achat.id) / (
                SELECT COUNT(id)
                FROM achat
                WHERE type_marche = 1  AND fournisseurs.pme = 1 AND YEAR(date_saisie) = :year
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
            )) * 100, 2) AS VolumePercentPME,
            ROUND((SUM(achat.montant_achat) / (
                SELECT SUM(montant_achat)
                FROM achat
                WHERE type_marche = 1  AND fournisseurs.pme = 1 AND YEAR(date_saisie) = :year
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
            )) * 100, 2) AS  ValeurPercentPME
        FROM    
            achat
        JOIN
            fournisseurs ON achat.num_siret_id = fournisseurs.id
        WHERE
            achat.type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(date_saisie) = :year
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
        LIMIT 0, 100;";
        
    
    

$stmt = $conn->prepare($sql);
      
                $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
                $achats=$resultSet->fetchAllAssociative();
                return $achats;
    }
    public function statisticPMEMonth($form)
    {
        $userId = null;
        $numSiretId = null;
        $cpvId = null;
        $uOId = null;
        $formationId = null;

        $date = $form["date"]->getData();
        $user = $form["utilisateurs"]->getData();
        $numSiret = $form["num_siret"]->getData();
        $cpv = $form["code_cpv"]->getData();
        $uO = $form["code_uo"]->getData();
        $formation = $form["code_formation"]->getData();
        if ($user) {
            // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
            $userId = $user->getId();
        }
        if ($numSiret) {
            $numSiretId = $numSiret->getId();
        }
        if ($cpv) {
            $cpvId = $cpv->getId();
        }
        if ($uO) {
            $uOId = $uO->getId();
        }
        if ($formation) {
            $formationId = $formation->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT
        MONTH(achat.date_saisie) AS mois,
        SUM(CASE WHEN achat.type_marche = 1 THEN 1 ELSE 0 END) AS nombre_achats_type_marche_1,
        COUNT(*) AS nombre_total_achats_pme,
        ROUND(
            SUM(CASE WHEN achat.type_marche = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 0
        ) AS pourcentage_achats_type_marche_1
    FROM
        achat
    JOIN
        fournisseurs ON achat.num_siret_id = fournisseurs.id
    WHERE
    fournisseurs.pme = 1 AND YEAR(achat.date_saisie) = :year
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
    GROUP BY
        mois
    ORDER BY
        FIELD(mois, '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12')
    LIMIT 0, 100";

$stmt = $conn->prepare($sql);
                
                $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
                $achats=$resultSet->fetchAllAssociative();
                return $achats;
    }
    public function statisticPMETopVol($form)
    {
        $userId = null;
        $numSiretId = null;
        $cpvId = null;
        $uOId = null;
        $formationId = null;

        $date = $form["date"]->getData();
        $user = $form["utilisateurs"]->getData();
        $numSiret = $form["num_siret"]->getData();
        $cpv = $form["code_cpv"]->getData();
        $uO = $form["code_uo"]->getData();
        $formation = $form["code_formation"]->getData();
        if ($user) {
            // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
            $userId = $user->getId();
        }
        if ($numSiret) {
            $numSiretId = $numSiret->getId();
        }
        if ($cpv) {
            $cpvId = $cpv->getId();
        }
        if ($uO) {
            $uOId = $uO->getId();
        }
        if ($formation) {
            $formationId = $formation->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT
        SUBSTRING(fournisseurs.code_postal, 1, 2) AS departement,
        COUNT(achat.id) AS total_nombre_achats
    FROM
        achat
    JOIN
        fournisseurs ON achat.num_siret_id = fournisseurs.id
    WHERE
        achat.type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(achat.date_saisie) = :year
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
    GROUP BY
        departement
    ORDER BY
        total_nombre_achats DESC
    LIMIT 5;
    ";
        
    
    

$stmt = $conn->prepare($sql);
                
                $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
                $achats=$resultSet->fetchAllAssociative();
                
                return $achats;
    }
    public function statisticPMETopVal($form)
    {
        $userId = null;
        $numSiretId = null;
        $cpvId = null;
        $uOId = null;
        $formationId = null;

        $date = $form["date"]->getData();
        $user = $form["utilisateurs"]->getData();
        $numSiret = $form["num_siret"]->getData();
        $cpv = $form["code_cpv"]->getData();
        $uO = $form["code_uo"]->getData();
        $formation = $form["code_formation"]->getData();
        if ($user) {
            // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
            $userId = $user->getId();
        }
        if ($numSiret) {
            $numSiretId = $numSiret->getId();
        }
        if ($cpv) {
            $cpvId = $cpv->getId();
        }
        if ($uO) {
            $uOId = $uO->getId();
        }
        if ($formation) {
            $formationId = $formation->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT
        SUBSTRING(fournisseurs.code_postal, 1, 2) AS departement,
        ROUND(SUM(achat.montant_achat), 0) AS somme_montant_achat
    FROM
        achat
    JOIN
        fournisseurs ON achat.num_siret_id = fournisseurs.id
    WHERE
        achat.type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(achat.date_saisie) = :year
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
    GROUP BY
        departement
    ORDER BY
        somme_montant_achat DESC
    LIMIT 5;
    ";
        
    
    

$stmt = $conn->prepare($sql);
                
                $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
                $achats=$resultSet->fetchAllAssociative();
                return $achats;
    }
    //getCountsByDateAndType est une méthode qui récupère et compte les données
    //dans une base de données selon le mois de la date de saisie,
    //tout en regroupant les résultats par mois. Elle prend en compte plusieurs
    //filtres comme le type de marché, l'état d'achat, et des informations d'utilisateur
    //comme le numéro SIRET, le code UO, le code CPV et le code de formation.
    //prepareCountsArray est une méthode qui transforme le résultat en un tableau avec
    //le nombre d'éléments et la somme totale pour chaque mois de l'année.
    public function searchAchat($form)
    {
        $data = $form->getData();
        $queryBuilder = $this->createQueryBuilder('b');
        $achatmin = $form["montant_achat_min"]->getData();
        $user = $this->security->getUser();   
        if ($form['etat_achat']->getData() === "EC") {
            // Convertir la chaîne "0" en valeur numérique 0
            $etat = 0;
        } 
        switch ($form['etat_achat']->getData()) {
            case 'EC':
                $etat = 0;
                break;
            case 'V':
                $etat = 2;
                break;
            case 'A':
                $etat = 1;
                break;
            default:
                // Gérer le cas par défaut ici, si nécessaire
                break;
        }
    
    switch ($form['type_marche']->getData()) {
        case 'MABC':
            $type = 0;
            break;
        case 'MPPA':
            $type = 1;
            break;
        default:
            // Gérer le cas par défaut ici, si nécessaire
            break;
    }
    switch ($form['devis']->getData()) {
        case 'Pr':
            $devis = 0;
            break;
        case 'Gs':
            $devis = 1;
            break;
        default:
            // Gérer le cas par défaut ici, si nécessaire
            break;
    }
        $queryBuilder
            ->select('b')
            ->Where('b.utilisateurs = :utilisateurs')
            ->setParameter('utilisateurs', $user);

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
                    ->setParameter('etat_achat',$etat);
            }
            if ($form["devis"]->getData()) {
                $queryBuilder
                    ->andWhere('b.devis = :devis')
                    ->setParameter('devis', $devis);
            }
            if ($form["type_marche"]->getData()) {
                $queryBuilder
                    ->andWhere('b.type_marche = :type_marche')
                    ->setParameter('type_marche', $type);
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
            if ($form["numero_ej"]->getData()) {
                $queryBuilder
                    ->andWhere('b.numero_ej LIKE :numero_ej')
                    ->setParameter('numero_ej', '%' . $form["numero_ej"]->getData() . '%');
            }
            if ($form["debut_rec"]->getData()) {
                $queryBuilder
                    ->andWhere('b.date_saisie > :debut_rec')
                    ->andWhere('b.date_saisie < :fin_rec')
                    ->setParameter('debut_rec',  $form["debut_rec"]->getData()->format('Y-m-d') )
                    ->setParameter('fin_rec',   $form["fin_rec"]->getData()->format('Y-m-d') );
            }
        // ... Votre logique de construction de la requête ici ...
        $queryBuilder->orderBy('b.date_saisie', 'DESC');

        $query = $queryBuilder->getQuery();
    
        return $query;
    }
    public function extractSearchAchat($form)
    {
        $data = $form->getData();
        $queryBuilder = $this->createQueryBuilder('b');
        $achatmin = $form["montant_achat_min"]->getData();
        $user = $this->security->getUser();   
        if ($form['etat_achat']->getData() === "EC") {
            // Convertir la chaîne "0" en valeur numérique 0
            $etat = 0;
        } 
        switch ($form['etat_achat']->getData()) {
            case 'EC':
                $etat = 0;
                break;
            case 'V':
                $etat = 2;
                break;
            case 'A':
                $etat = 1;
                break;
            default:
                // Gérer le cas par défaut ici, si nécessaire
                break;
        }
    
    switch ($form['type_marche']->getData()) {
        case 'MABC':
            $type = 0;
            break;
        case 'MPPA':
            $type = 1;
            break;
        default:
            // Gérer le cas par défaut ici, si nécessaire
            break;
    }
    switch ($form['devis']->getData()) {
        case 'Pr':
            $devis = 0;
            break;
        case 'Gs':
            $devis = 1;
            break;
        default:
            // Gérer le cas par défaut ici, si nécessaire
            break;
    }
        //    dd($form['date_valid_inter_attr']->getData() == true);
            // Vérifier si la case 'objet_achat_attr' est cochée
            

            
           

            $selectFields[] = $form['chrono_attr']->getData() == true ? 'b.numero_achat' : null;
            $selectFields[] = $form['id_dem_achat_attr']->getData() == true ? 'b.id_demande_achat' : null;
            $selectFields[] = $form['date_sillage_attr']->getData() == true ? 'b.date_sillage' : null;
            $selectFields[] = $form['date_commande_chorus_attr']->getData() == true ? 'b.date_commande_chorus' : null;
            $selectFields[] = $form['date_valid_inter_attr']->getData() == true ? 'b.date_valid_inter' : null;
            $selectFields[] = $form['date_valid_attr']->getData() == true ? 'b.date_validation' : null;
            $selectFields[] = $form['date_notif_attr']->getData() == true ? 'b.date_notification' : null;
            $selectFields[] = $form['date_annul_attr']->getData() == true ? 'b.date_annulation' : null;
            $selectFields[] = $form['ej_attr']->getData() == true ? 'b.numero_ej' : null;
            $selectFields[] = $form['objet_achat_attr']->getData() == true ? 'b.objet_achat' : null;
            $selectFields[] = $form['type_marche_attr']->getData() == true ? 'b.type_marche' : null;
            $selectFields[] = $form['montant_ht_attr']->getData() == true ? 'b.montant_achat' : null;
            $selectFields[] = $form['montant_ttc_attr']->getData() == true ? 'b.montant_achat   ' : null;
            $selectFields[] = $form['devis_attr']->getData() == true ? 'b.devis' : null;
            $selectFields[] = $form['obs_attr']->getData() == true ? 'b.observations' : null;
            $selectFields[] = $form['etat_achat_attr']->getData() == true ? 'b.etat_achat' : null;
            $selectFields[] = $form['place_attr']->getData() == true ? 'b.place' : null;
            $selectFields = array_filter($selectFields);

        $queryBuilder
            ->select($selectFields)
            ->Where('b.utilisateurs = :utilisateurs')
            ->setParameter('utilisateurs', $user);

           
            $form['code_acheteur_attr']->getData() == true ? $queryBuilder->addSelect('IDENTITY(b.utilisateurs) as utilisateurs_id') : null;
            $form['nom_acheteur_attr']->getData() == true ? $queryBuilder->leftJoin('b.utilisateurs', 'u')->addSelect('u.nom_utilisateur') : null;

            $form['code_formation_attr']->getData() == true ? $queryBuilder->addSelect('IDENTITY(b.code_formation) as code_formation_id') : null;
            $form['libelle_formation_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_formation', 'g')->addSelect('g.libelle_formation') : null;

            $form['code_uo_attr']->getData() == true ? $queryBuilder->addSelect('IDENTITY(b.code_uo) as code_uo_id') : null;
            $form['libelle_uo_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_uo', 'o')->addSelect('o.libelle_uo') : null;

            $form['code_cpv_attr']->getData() == true ? $queryBuilder->addSelect('IDENTITY(b.code_cpv) as code_cpv_id') : null;
            $form['libelle_cpv_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_cpv', 'c')->addSelect('c.libelle_cpv') : null;

            $form['tva_attr']->getData() == true ? $queryBuilder->addSelect('IDENTITY(b.tva_ident) as tva_ident_id') : null;


            if ($form['chorus_fournisseur_attr']->getData() == true ||  $form['code_client_fournisseur_attr']->getData() == true ||  $form['ville_fournisseur_attr']->getData() == true 
            ||  $form['cp_fournisseur_attr']->getData() == true||  $form['pme_fournisseurs_attr']->getData() == true ||  $form['tel_fournisseur_attr']->getData() == true 
            ||  $form['fax_fournisseur_attr']->getData() == true||  $form['mail_fournisseur_attr']->getData() == true||  $form['siret_fournisseur_attr']->getData() == true||  $form['nom_fournisseur_attr']->getData() == true ) {

                $queryBuilder->leftJoin('b.num_siret', 'f'); // Jointure avec la table 'fournisseurs'
    
               
                $form['ville_fournisseur_attr']->getData() == true ? $queryBuilder->addSelect('f.ville') : null;
                $form['cp_fournisseur_attr']->getData() == true ? $queryBuilder->addSelect('f.code_postal') : null;
                $form['pme_fournisseurs_attr']->getData() == true ? $queryBuilder->addSelect('f.pme') : null;
                $form['tel_fournisseur_attr']->getData() == true ? $queryBuilder->addSelect('f.tel') : null;
                $form['fax_fournisseur_attr']->getData() == true ? $queryBuilder->addSelect('f.FAX') : null;
                $form['mail_fournisseur_attr']->getData() == true ? $queryBuilder->addSelect('f.mail') : null;
                $form['siret_fournisseur_attr']->getData() == true ? $queryBuilder->addSelect('f.num_siret') : null;
                $form['nom_fournisseur_attr']->getData() == true ? $queryBuilder->addSelect('f.nom_fournisseur') : null;
                
            }
            if ($form['nom_service_attr']->getData() == true ||  $form['code_service_attr'] == true) {

                $queryBuilder->leftJoin('b.code_service', 's'); // Jointure avec la table 'service'
                $selectFields[] = $form['code_service_attr']->getData() == true ? $queryBuilder->addSelect('s.code_service') : null;
                $selectFields[] = $form['nom_service_attr']->getData() == true ?  $queryBuilder->addSelect('s.nom_service') : null;
            }


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
                    ->setParameter('etat_achat',$etat);
            }
            if ($form["devis"]->getData()) {
                $queryBuilder
                    ->andWhere('b.devis = :devis')
                    ->setParameter('devis', $devis);
            }
            if ($form["type_marche"]->getData()) {
                $queryBuilder
                    ->andWhere('b.type_marche = :type_marche')
                    ->setParameter('type_marche', $type);
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
            if ($form["numero_ej"]->getData()) {
                $queryBuilder
                    ->andWhere('b.numero_ej LIKE :numero_ej')
                    ->setParameter('numero_ej', '%' . $form["numero_ej"]->getData() . '%');
            }
            if ($form["debut_rec"]->getData()) {
                $queryBuilder
                    ->andWhere('b.date_saisie > :debut_rec')
                    ->andWhere('b.date_saisie < :fin_rec')
                    ->setParameter('debut_rec',  $form["debut_rec"]->getData()->format('Y-m-d') )
                    ->setParameter('fin_rec',   $form["fin_rec"]->getData()->format('Y-m-d') );
            }
        // ... Votre logique de construction de la requête ici ...
        $queryBuilder->orderBy('b.date_saisie', 'DESC');

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
   
    public function yearDelayDiff($form)
    {
        $userId = null;
        $numSiretId = null;
        $cpvId = null;
        $uOId = null;
        $formationId = null;

        $date = $form["date"]->getData();
        $user = $form["utilisateurs"]->getData();
        $numSiret = $form["num_siret"]->getData();
        $cpv = $form["code_cpv"]->getData();
        $uO = $form["code_uo"]->getData();
        $formation = $form["code_formation"]->getData();
        $jourcalendar = $form["jourcalendar"]->getData();
        if ($user) {
            // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
            $userId = $user->getId();
        }
        if ($numSiret) {
            $numSiretId = $numSiret->getId();
        }
        if ($cpv) {
            $cpvId = $cpv->getId();
        }
        if ($uO) {
            $uOId = $uO->getId();
        }
        if ($formation) {
            $formationId = $formation->getId();
        }
        $conn = $this->getEntityManager()->getConnection();
        if($jourcalendar=="jO"){

        
        $sql = "
        SELECT
        source,
        ROUND(AVG(CASE WHEN MONTH(date_saisie) = 1 THEN difference END), 1) AS Mois_1,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 2 THEN difference END), 1) AS Mois_2,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 3 THEN difference END), 1) AS Mois_3,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 4 THEN difference END), 1) AS Mois_4,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 5 THEN difference END), 1) AS Mois_5,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 6 THEN difference END), 1) AS Mois_6,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 7 THEN difference END), 1) AS Mois_7,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 8 THEN difference END), 1) AS Mois_8,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 9 THEN difference END), 1) AS Mois_9,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 10 THEN difference END), 1) AS Mois_10,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 11 THEN difference END), 1) AS Mois_11,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 12 THEN difference END), 1) AS Mois_12
      FROM (
        SELECT
          'ANT GSBDD' AS source,
          (DATEDIFF(date_commande_chorus, date_sillage) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_commande_chorus AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
          date_saisie
        FROM achat
        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
        UNION ALL
      
        SELECT
          'BUDGET' AS source,
          (DATEDIFF(date_valid_inter, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_valid_inter AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
          date_saisie
        FROM achat
        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
        UNION ALL
      
        SELECT
          'APPRO' AS source,
          (DATEDIFF(date_validation, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_valid_inter AND date_validation AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
          date_saisie
        FROM achat
        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
        UNION ALL
      
        SELECT
          'FIN' AS source,
          (DATEDIFF(date_notification, date_validation) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_validation AND date_notification AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
          date_saisie
        FROM achat
        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
        UNION ALL
      
        SELECT
          'PFAF' AS source,
          (DATEDIFF(date_notification, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_notification AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
          date_saisie
        FROM achat
        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
        UNION ALL
      
        SELECT
          'Chorus formul.' AS source,
          (DATEDIFF(date_notification, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_notification AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
          date_saisie
        FROM achat
        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
    " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
    " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
    " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
    " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
    ) AS combined_data
      GROUP BY source
      ORDER BY
        source = 'ANT GSBDD' DESC,
        source = 'BUDGET' DESC,
        source = 'APPRO' DESC,
        source = 'FIN' DESC,
        source = 'PFAF' DESC,
        source = 'Chorus formul.' DESC
      LIMIT 0,100
            ";
        }
        else{
            $sql = "SELECT
            source,
            ROUND(AVG(CASE WHEN MONTH(date_saisie) = 1 THEN difference END), 1) AS Mois_1,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 2 THEN difference END), 1) AS Mois_2,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 3 THEN difference END), 1) AS Mois_3,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 4 THEN difference END), 1) AS Mois_4,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 5 THEN difference END), 1) AS Mois_5,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 6 THEN difference END), 1) AS Mois_6,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 7 THEN difference END), 1) AS Mois_7,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 8 THEN difference END), 1) AS Mois_8,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 9 THEN difference END), 1) AS Mois_9,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 10 THEN difference END), 1) AS Mois_10,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 11 THEN difference END), 1) AS Mois_11,
    ROUND(AVG(CASE WHEN MONTH(date_saisie) = 12 THEN difference END), 1) AS Mois_12
          FROM (
            SELECT
              'ANT GSBDD' AS source,
              (DATEDIFF(date_commande_chorus, date_sillage)) AS difference,
              date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
            UNION ALL 
            
            SELECT
              'BUDGET' AS source,
              (DATEDIFF(date_valid_inter, date_commande_chorus)) AS difference,
              date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
            UNION ALL
          
            SELECT
              'APPRO' AS source,
              (DATEDIFF(date_validation, date_valid_inter))  AS difference,
              date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
            UNION ALL
          
            SELECT
              'FIN' AS source,
              (DATEDIFF(date_notification, date_validation))  AS difference,
              date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
            UNION ALL
          
            SELECT
              'PFAF' AS source,
              (DATEDIFF(date_notification, date_valid_inter)) AS difference,
              date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
            UNION ALL
          
            SELECT
              'Chorus formul.' AS source,
              (DATEDIFF(date_notification, date_commande_chorus)) AS difference,
              date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
            " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "      
        ) AS combined_data
          GROUP BY source
          ORDER BY
            source = 'ANT GSBDD' DESC,
            source = 'BUDGET' DESC,
            source = 'APPRO' DESC,
            source = 'FIN' DESC,
            source = 'PFAF' DESC,
            source = 'Chorus formul.' DESC
          LIMIT 0,100
                ";
        }
            $stmt = $conn->prepare($sql);
  
            $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
            $achats=$resultSet->fetchAllAssociative();
           
            // dd($achats);
            return $achats;
        }
        public function yearDelayCount($form)
        {
            $userId = null;
            $numSiretId = null;
            $cpvId = null;
            $uOId = null;
            $formationId = null;
            $date = $form["date"]->getData();
            $user = $form["utilisateurs"]->getData();
            $numSiret = $form["num_siret"]->getData();
            $cpv = $form["code_cpv"]->getData();
            $uO = $form["code_uo"]->getData();
            $formation = $form["code_formation"]->getData();
            $jourcalendar = $form["jourcalendar"]->getData();

            if ($user) {
                // Si une valeur a été saisie, vous pouvez obtenir l'ID de l'utilisateur
                $userId = $user->getId();
            }
            if ($numSiret) {
                $numSiretId = $numSiret->getId();
            }
            if ($cpv) {
                $cpvId = $cpv->getId();
            }
            if ($uO) {
                $uOId = $uO->getId();
            }
            if ($formation) {
                $formationId = $formation->getId();
            }
            $conn = $this->getEntityManager()->getConnection();
            if($jourcalendar=="jO"){

            $sql = "SELECT
            source,
            ROUND(COUNT(CASE WHEN source = 'ANT GSBDD' AND difference <= 3 THEN 1 ELSE NULL END), 2) AS CountAntInf3,
    ROUND(COUNT(CASE WHEN source = 'ANT GSBDD' AND difference > 3 THEN 1 ELSE NULL END), 2) AS CountAntSup3,
    ROUND(COUNT(CASE WHEN source = 'BUDGET' AND difference <= 3 THEN 1 ELSE NULL END), 2) AS CountBudgetInf3,
    ROUND(COUNT(CASE WHEN source = 'BUDGET' AND difference > 3 THEN 1 ELSE NULL END), 2) AS CountBudgetSup3,
    ROUND(COUNT(CASE WHEN source = 'APPRO' AND difference <= 7 THEN 1 ELSE NULL END), 2) AS CountApproInf7,
    ROUND(COUNT(CASE WHEN source = 'APPRO' AND difference > 7 THEN 1 ELSE NULL END), 2) AS CountApproSup7,
    ROUND(COUNT(CASE WHEN source = 'FIN' AND difference <= 7 THEN 1 ELSE NULL END), 2) AS CountFinInf7,
    ROUND(COUNT(CASE WHEN source = 'FIN' AND difference > 7 THEN 1 ELSE NULL END), 2) AS CountFinSup7,
    ROUND(COUNT(CASE WHEN source = 'Chorus formul.' AND difference <= 10 THEN 1 ELSE NULL END), 2) AS CountChorusFormInf10,
    ROUND(COUNT(CASE WHEN source = 'Chorus formul.' AND difference > 10 THEN 1 ELSE NULL END), 2) AS CountChorusFormSup10,
    ROUND(COUNT(CASE WHEN source = 'PFAF' AND difference <= 14 THEN 1 ELSE NULL END), 2) AS CountPfafInf14,
    ROUND(COUNT(CASE WHEN source = 'PFAF' AND difference > 14 THEN 1 ELSE NULL END), 2) AS CountPfafSup14,
    ROUND((SUM(CASE WHEN source = 'ANT GSBDD' AND difference <= 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'ANT GSBDD' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_3_Jours_Ant,
    ROUND((SUM(CASE WHEN source = 'ANT GSBDD' AND difference > 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'ANT GSBDD' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_3_Jours_Ant,
    ROUND((SUM(CASE WHEN source = 'BUDGET' AND difference <= 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'BUDGET' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_3_Jours_Budget,
    ROUND((SUM(CASE WHEN source = 'BUDGET' AND difference > 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'BUDGET' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_3_Jours_Budget,
    ROUND((SUM(CASE WHEN source = 'APPRO' AND difference <= 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'APPRO' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_7_Jours_Appro,
    ROUND((SUM(CASE WHEN source = 'APPRO' AND difference > 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'APPRO' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_7_Jours_Appro,
    ROUND((SUM(CASE WHEN source = 'FIN' AND difference <= 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'FIN' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_7_Jours_Fin,
    ROUND((SUM(CASE WHEN source = 'FIN' AND difference > 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'FIN' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_7_Jours_Fin,
    ROUND((SUM(CASE WHEN source = 'Chorus formul.' AND difference <= 10 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'Chorus formul.' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_10_Jours_Chorus,
    ROUND((SUM(CASE WHEN source = 'Chorus formul.' AND difference > 10 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'Chorus formul.' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_10_Jours_Chorus,
    ROUND((SUM(CASE WHEN source = 'PFAF' AND difference <= 14 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'PFAF' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_14_Jours_Pfaf,
    ROUND((SUM(CASE WHEN source = 'PFAF' AND difference > 14 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'PFAF' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_14_Jours_Pfaf,
    ROUND(COUNT(CASE WHEN source IN ('Délai total') AND (difference <= 15) THEN 1 ELSE NULL END), 2) AS CountDelaiTotalInf15,
    ROUND(COUNT(CASE WHEN source IN ('Délai total') AND (difference > 15) THEN 1 ELSE NULL END), 2) AS CountDelaiTotalSup15,
    ROUND((SUM(CASE WHEN source IN ('Délai total') AND (difference <= 15) THEN 1 ELSE 0 END) / SUM(CASE WHEN source IN ('Délai total') THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_15_Jours,
    ROUND((SUM(CASE WHEN source IN ('Délai total') AND (difference > 15) THEN 1 ELSE 0 END) / SUM(CASE WHEN source IN ('Délai total') THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_15_Jours
                FROM
                    (
                        SELECT
                            'ANT GSBDD' AS source,
                            (DATEDIFF(date_commande_chorus, date_sillage) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_commande_chorus AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
                            date_saisie
                        FROM achat
                        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
        
                UNION ALL

                SELECT
                'BUDGET' AS source,
                (DATEDIFF(date_valid_inter, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_valid_inter AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
                date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
        
                UNION ALL
            
                SELECT
                    'APPRO' AS source,
                    (DATEDIFF(date_validation, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_valid_inter AND date_validation AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
                    date_saisie
                FROM achat
                WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
        
                UNION ALL
                SELECT
                'FIN' AS source,
                (DATEDIFF(date_notification, date_validation) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_validation AND date_notification AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
                date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
        
                UNION ALL
            
                SELECT
                    'PFAF' AS source,
                    (DATEDIFF(date_notification, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_valid_inter AND date_notification AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
                    date_saisie
                FROM achat
                WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
        
                UNION ALL
            
                SELECT
                'Chorus formul.' AS source,
                (DATEDIFF(date_notification, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_notification AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
                date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "

                UNION ALL
            
                SELECT
                'Délai total' AS source,
                (DATEDIFF(date_validation, date_sillage) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_validation AND DAYOFWEEK(start) NOT IN (1, 7))) AS difference,
                date_saisie
            FROM achat
            WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
                
        
                ) AS combined_data
                WHERE source IN ('ANT GSBDD', 'BUDGET', 'APPRO', 'FIN', 'Chorus formul.', 'PFAF', 'Délai total')
            GROUP BY source
            ORDER BY
                source = 'ANT GSBDD' DESC,
                source = 'BUDGET' DESC,
                source = 'APPRO' DESC,
                source = 'FIN' DESC,
                source = 'Chorus formul.' DESC,
                source = 'PFAF' DESC,
                source = 'Délai total' DESC
            LIMIT 0,100
                ";
                    }
                    else{
                        $sql="SELECT
                        source,
                        COUNT(CASE WHEN source = 'ANT GSBDD' AND difference <= 3 THEN 1 ELSE NULL END) AS CountAntInf3,
    COUNT(CASE WHEN source = 'ANT GSBDD' AND difference > 3 THEN 1 ELSE NULL END) AS CountAntSup3,
    COUNT(CASE WHEN source = 'BUDGET' AND difference <= 3 THEN 1 ELSE NULL END) AS CountBudgetInf3,
    COUNT(CASE WHEN source = 'BUDGET' AND difference > 3 THEN 1 ELSE NULL END) AS CountBudgetSup3,
    COUNT(CASE WHEN source = 'APPRO' AND difference <= 7 THEN 1 ELSE NULL END) AS CountApproInf7,
    COUNT(CASE WHEN source = 'APPRO' AND difference > 7 THEN 1 ELSE NULL END) AS CountApproSup7,
    COUNT(CASE WHEN source = 'FIN' AND difference <= 7 THEN 1 ELSE NULL END) AS CountFinInf7,
    COUNT(CASE WHEN source = 'FIN' AND difference > 7 THEN 1 ELSE NULL END) AS CountFinSup7,
    COUNT(CASE WHEN source = 'Chorus formul.' AND difference <= 10 THEN 1 ELSE NULL END) AS CountChorusFormInf10,
    COUNT(CASE WHEN source = 'Chorus formul.' AND difference > 10 THEN 1 ELSE NULL END) AS CountChorusFormSup10,
    COUNT(CASE WHEN source = 'PFAF' AND difference <= 14 THEN 1 ELSE NULL END) AS CountPfafInf14,
    COUNT(CASE WHEN source = 'PFAF' AND difference > 14 THEN 1 ELSE NULL END) AS CountPfafSup14,
    ROUND((SUM(CASE WHEN source = 'ANT GSBDD' AND difference <= 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'ANT GSBDD' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_3_Jours_Ant,
    ROUND((SUM(CASE WHEN source = 'ANT GSBDD' AND difference > 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'ANT GSBDD' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_3_Jours_Ant,
    ROUND((SUM(CASE WHEN source = 'BUDGET' AND difference <= 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'BUDGET' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_3_Jours_Budget,
    ROUND((SUM(CASE WHEN source = 'BUDGET' AND difference > 3 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'BUDGET' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_3_Jours_Budget,
    ROUND((SUM(CASE WHEN source = 'APPRO' AND difference <= 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'APPRO' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_7_Jours_Appro,
    ROUND((SUM(CASE WHEN source = 'APPRO' AND difference > 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'APPRO' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_7_Jours_Appro,
    ROUND((SUM(CASE WHEN source = 'FIN' AND difference <= 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'FIN' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_7_Jours_Fin,
    ROUND((SUM(CASE WHEN source = 'FIN' AND difference > 7 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'FIN' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_7_Jours_Fin,
    ROUND((SUM(CASE WHEN source = 'Chorus formul.' AND difference <= 10 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'Chorus formul.' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_10_Jours_Chorus,
    ROUND((SUM(CASE WHEN source = 'Chorus formul.' AND difference > 10 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'Chorus formul.' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_10_Jours_Chorus,
    ROUND((SUM(CASE WHEN source = 'PFAF' AND difference <= 14 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'PFAF' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_14_Jours_Pfaf,
    ROUND((SUM(CASE WHEN source = 'PFAF' AND difference > 14 THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'PFAF' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_14_Jours_Pfaf,
    COUNT(CASE WHEN source IN ('Délai total') AND (difference <= 15) THEN 1 ELSE NULL END) AS CountDelaiTotalInf15,
    COUNT(CASE WHEN source IN ('Délai total') AND (difference > 15) THEN 1 ELSE NULL END) AS CountDelaiTotalSup15,
    ROUND((SUM(CASE WHEN source = 'Délai total' AND (difference <= 15) THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'Délai total' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Inf_15_Jours,
    ROUND((SUM(CASE WHEN source = 'Délai total' AND (difference > 15) THEN 1 ELSE 0 END) / SUM(CASE WHEN source = 'Délai total' THEN 1 ELSE 0 END)) * 100, 2) AS Pourcentage_Delai_Sup_15_Jours
                      FROM (
                        SELECT
                          'ANT GSBDD' AS source,
                          DATEDIFF(date_commande_chorus, date_sillage) AS difference,
                          date_saisie
                        FROM achat
                        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
                
                        UNION ALL
                      
                        SELECT
                          'BUDGET' AS source,
                          DATEDIFF(date_valid_inter, date_commande_chorus) AS difference,
                          date_saisie
                        FROM achat
                        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
                
                        UNION ALL
                      
                        SELECT
                          'APPRO' AS source,
                          DATEDIFF(date_validation, date_valid_inter) AS difference,
                          date_saisie
                        FROM achat
                        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
                
                        UNION ALL
                      
                        SELECT
                          'FIN' AS source,
                          DATEDIFF(date_notification, date_validation) AS difference,
                          date_saisie
                        FROM achat
                        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
                
                        UNION ALL
                      
                        SELECT
                          'PFAF' AS source,
                          DATEDIFF(date_notification, date_valid_inter) AS difference,
                          date_saisie
                        FROM achat
                        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
                
                        UNION ALL
                      
                        SELECT
                          'Chorus formul.' AS source,
                          DATEDIFF(date_notification, date_commande_chorus) AS difference,
                          date_saisie
                        FROM achat
                        WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "

                        UNION ALL
            
                        SELECT
                        'Délai total' AS source,
                        (DATEDIFF(date_validation, date_sillage)) AS difference,
                        date_saisie
                    FROM achat
                    WHERE YEAR(date_saisie) = :year AND etat_achat = 2
                        " . ($userId !== null ? "AND utilisateurs_id = :userId" : "") . "
                        " . ($numSiretId !== null ? "AND num_siret_id = :numSiretId" : "") . "
                        " . ($cpvId !== null ? "AND code_cpv_id = :cpvId" : "") . "
                        " . ($uOId !== null ? "AND code_uo_id = :uOId" : "") . "
                        " . ($formationId !== null ? "AND code_formation_id = :formationId" : "") . "
                      ) AS combined_data
                      WHERE source IN ('ANT GSBDD', 'BUDGET', 'APPRO', 'FIN','Chorus formul.', 'PFAF', 'Délai total')
            
                      GROUP BY source
                      -- Organisez les sources dans l'ordre d'apparition
                      ORDER BY
                        source = 'ANT GSBDD' DESC,
                        source = 'BUDGET' DESC,
                        source = 'APPRO' DESC,
                        source = 'FIN' DESC,
                        source = 'Chorus formul.' DESC,
                        source = 'PFAF' DESC,
                        source = 'Délai total' DESC
                      LIMIT 0,100";
                    }
                $stmt = $conn->prepare($sql);
      
                $resultSet = $conn->executeQuery($sql, ['year' => $date, 'userId' => $userId,'numSiretId'=>$numSiretId,'cpvId'=>$cpvId,'uOId'=>$uOId,'formationId'=>$formationId]);
                $achats=$resultSet->fetchAllAssociative();
               
                // dd($achats);
                return $achats;
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
    public function valid( Request $request,$id)
    {
        // $val2 = $form["val"]->getData();
        $val = $request->request->get('val');
        // dd($val);
        $not = $request->request->get('not');
        $ej = $request->request->get('ej');
        $queryBuilder = $this->createQueryBuilder('u');
        $query = $queryBuilder->update(Achat::class, 'u')
            ->set('u.etat_achat', ':etat_achat')
            ->set('u.date_validation', ':date_validation')
            ->set('u.date_notification', ':date_notification')
            ->set('u.numero_ej', ':numero_ej')
            ->where('u.id = :id')
            ->setParameter('etat_achat', 2)
            ->setParameter('date_validation', $val)
            ->setParameter('date_notification', $not)
            ->setParameter('numero_ej', $ej)
            ->setParameter('id', $id)
            ->getQuery();
        $result = $query->execute();
    }

    public function add($achat)
    {
        $user = $this->security->getUser();    
            $date = new DateTime('now', new \DateTimeZone('Europe/Paris'));
            $achat->setUtilisateurs($user);
            $achat->setDateSaisie($date);
            $achat->setEtatAchat(0);
            $this->entityManager->persist($achat);
            $this->entityManager->flush();

        
    }
    public function cancel($id)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder->update(Achat::class, 'u')
            ->set('u.etat_achat', ':etat_achat')
            ->where('u.id = :id')
            ->setParameter('etat_achat', 1)
            ->setParameter('id', $id)
            ->getQuery();
        $result = $query->execute();

        
    }
    public function reint($id)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder->update(Achat::class, 'u')
            ->set('u.etat_achat', ':etat_achat')
            ->where('u.id = :id')
            ->setParameter('etat_achat', 0)
            ->setParameter('id', $id)
            ->getQuery();
        $result = $query->execute();

        
    }




}