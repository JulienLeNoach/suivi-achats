<?php

namespace App\Repository;

use DateTime;
use App\Entity\Achat;
use App\Factory\AchatFactory;
use Doctrine\ORM\Query\Expr\Join;
use App\Service\AchatNumberService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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
    private $achatFactory;
    private $entityManager;
    private $achatNumberService;

    public function __construct(ManagerRegistry $registry,Security $security,AchatFactory $achatFactory,EntityManagerInterface $entityManager,AchatNumberService $achatNumberService)
    {
        parent::__construct($registry, Achat::class);
        $this->security = $security;
        $this->achatFactory = $achatFactory;
        $this->entityManager = $entityManager;
        $this->achatNumberService = $achatNumberService;


    }
    private function extractCriteriaFromForm($form)
{
    $criteria = [
        'date' => $form['date']->getData(),
        'parameters' => [],
        'conditions' => ''
    ];

    $fields = ['utilisateurs', 'num_siret', 'code_cpv', 'code_uo', 'code_formation'];

    foreach ($fields as $field) {
        $value = $form[$field]->getData();
        $idName = $field . '_id';

        if ($value) {
            $criteria[$idName] = $value->getId();
            $criteria['parameters'][$idName] = $criteria[$idName];
            $criteria['conditions'] .= " AND {$idName} = :{$idName}";
        }
    }

    return $criteria;
}

 public function getPurchaseByType($form)
{
    $criteria = $this->extractCriteriaFromForm($form);

    $conn = $this->getEntityManager()->getConnection();
    $sql = "
        SELECT
            type_marche,
            COUNT(*) AS nombre_achats,
            COUNT(CASE WHEN type_marche = 0 THEN 1 END) AS nombre_achats_type_0,
            COUNT(CASE WHEN type_marche = 1 THEN 1 END) AS nombre_achats_type_1,
            ROUND((COUNT(CASE WHEN type_marche = 0 THEN 1 END) / NULLIF((SELECT COUNT(*) FROM achat WHERE YEAR(date_notification) = :year AND etat_achat = 2 {$criteria['conditions']}), 0)) * 100, 2) AS pourcentage_type_0,
            ROUND((COUNT(CASE WHEN type_marche = 1 THEN 1 END) / NULLIF((SELECT COUNT(*) FROM achat WHERE YEAR(date_notification) = :year AND etat_achat = 2 {$criteria['conditions']}), 0)) * 100, 2) AS pourcentage_type_1,
            ROUND(SUM(CASE WHEN type_marche = 0 THEN montant_achat ELSE 0 END), 2) AS somme_montant_type_0,
            ROUND(AVG(CASE WHEN type_marche = 0 THEN montant_achat ELSE NULL END), 2) AS moyenne_montant_type_0,
            ROUND(SUM(CASE WHEN type_marche = 1 THEN montant_achat ELSE 0 END), 2) AS somme_montant_type_1,
            ROUND(AVG(CASE WHEN type_marche = 1 THEN montant_achat ELSE NULL END), 2) AS moyenne_montant_type_1,
            ROUND((SUM(CASE WHEN type_marche = 0 THEN montant_achat ELSE 0 END) / NULLIF((SELECT SUM(montant_achat) FROM achat WHERE YEAR(date_notification) = :year AND etat_achat = 2 {$criteria['conditions']}), 0)) * 100, 2) AS pourcentage_type_0_total,
            ROUND((SUM(CASE WHEN type_marche = 1 THEN  montant_achat ELSE 0 END) / NULLIF((SELECT SUM(montant_achat) FROM achat WHERE YEAR(date_notification) = :year AND etat_achat = 2 {$criteria['conditions']}), 0)) * 100, 2) AS pourcentage_type_1_total,
            (SELECT COUNT(*) FROM achat WHERE YEAR(date_notification) = :year AND etat_achat = 2 {$criteria['conditions']} ) AS nombre_total_achats
        FROM
            achat
        WHERE
            type_marche IN (0, 1) AND YEAR(date_notification) = :year AND etat_achat = 2 
            {$criteria['conditions']}
        GROUP BY
            type_marche
        LIMIT 0, 100;";

    $stmt = $conn->prepare($sql);
    $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
    $achats = $resultSet->fetchAllAssociative();
    return $achats;
}


public function getPurchaseByTypeMount($form)
{
    $criteria = $this->extractCriteriaFromForm($form);

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
            type_marche IN (0, 1) AND YEAR(date_notification) = :year AND etat_achat = 2 
            {$criteria['conditions']}
        GROUP BY
            type_marche;";

    $stmt = $conn->prepare($sql);
    $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
    $achats = $resultSet->fetchAllAssociative();

    return $achats;
}
public function getPMESum($form)
{
    $criteria = $this->extractCriteriaFromForm($form);

    $conn = $this->getEntityManager()->getConnection();
    $sql = "
        SELECT
            ROUND(COUNT(achat.id), 2) AS VolumePME,
            ROUND(SUM(achat.montant_achat), 2) AS ValeurPME,
            ROUND((COUNT(achat.id) / (
                SELECT COUNT(id)
                FROM achat
                WHERE type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(date_notification) = :year AND etat_achat = 2 
                {$criteria['conditions']}
            )) * 100, 2) AS VolumePercentPME,
            ROUND((SUM(achat.montant_achat) / (
                SELECT SUM(montant_achat)
                FROM achat
                WHERE type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(date_notification) = :year AND etat_achat = 2 
                {$criteria['conditions']}
            )) * 100, 2) AS ValeurPercentPME
        FROM    
            achat
        JOIN
            fournisseurs ON achat.num_siret_id = fournisseurs.id
        WHERE
            achat.type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(date_notification) = :year AND etat_achat = 2 
            {$criteria['conditions']}
        LIMIT 0, 100;";

    $stmt = $conn->prepare($sql);
    $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
    $achats = $resultSet->fetchAllAssociative();

    return $achats;
}
public function getPMEMonthSum($form)
{
    $criteria = $this->extractCriteriaFromForm($form);

    $conn = $this->getEntityManager()->getConnection();
    $sql = "
    SELECT
        MONTH(achat.date_notification) AS mois,
        COUNT(CASE WHEN fournisseurs.pme = 1 AND achat.type_marche = 1 AND etat_achat = 2 THEN 1 ELSE NULL END) AS nombre_achats_pme_type_marche_1,
        COUNT(CASE WHEN fournisseurs.pme = 1 AND etat_achat = 2 THEN 1 ELSE NULL END) AS nombre_achats_pme,
        COUNT(CASE WHEN fournisseurs.pme = 1 AND etat_achat = 2 AND achat.type_marche = 1 THEN 1 ELSE NULL END) / COUNT(CASE WHEN achat.type_marche = 1 THEN 1 ELSE NULL END) * 100 AS pourcentage_achats_type_marche_1
    FROM
        achat
    JOIN
        fournisseurs ON achat.num_siret_id = fournisseurs.id
    WHERE
        YEAR(achat.date_notification) = :year
        {$criteria['conditions']}
    GROUP BY
        mois
    ORDER BY
        mois
    LIMIT 0, 100";



    $stmt = $conn->prepare($sql);
    $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
    $achats = $resultSet->fetchAllAssociative();

    return $achats;
}
public function getPMETopVol($form)
{
    $criteria = $this->extractCriteriaFromForm($form);

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
            achat.type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(achat.date_notification) = :year AND etat_achat = 2 
            {$criteria['conditions']}
        GROUP BY
            departement
        ORDER BY
            total_nombre_achats DESC
        LIMIT 5;
    ";

    $stmt = $conn->prepare($sql);
    $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
    $achats = $resultSet->fetchAllAssociative();

    return $achats;
}
public function getPMETopVal($form)
{
    $criteria = $this->extractCriteriaFromForm($form);

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
            achat.type_marche = 1 AND fournisseurs.pme = 1 AND YEAR(achat.date_notification) = :year AND etat_achat = 2 
            {$criteria['conditions']}
        GROUP BY
            departement
        ORDER BY
            somme_montant_achat DESC
        LIMIT 5;
    ";

    $stmt = $conn->prepare($sql);
    $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
    $achats = $resultSet->fetchAllAssociative();

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
        // dd($form["zipcode"]);
        $queryBuilder = $this->createQueryBuilder('b');
        $montantAchatMin = $form["montant_achat_min"]->getData();
        $montantAchatMax = $form["montant_achat"]->getData();
        $user = $this->security->getUser();   
        // dd($form);
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

        $queryBuilder
            ->select('b');
            if($form['all_user']->getData()==false){
                $queryBuilder
            ->Where('b.utilisateurs = :utilisateurs')
            ->setParameter('utilisateurs', $user);
        }
        $queryBuilder->leftJoin('b.code_formation', 'f');
        $queryBuilder->leftJoin('b.utilisateurs', 'u');
        $queryBuilder->leftJoin('b.num_siret', 'n');

            // if ($form["objet_achat"]->getData()){
            //     $queryBuilder
            //         ->andWhere('b.objet_achat LIKE :objet_achat')
            //         ->setParameter('objet_achat', '%' . $data->getObjetAchat() . '%');
            // }

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
            if ($form["numero_achat"]->getData()) {
                $queryBuilder
                    ->andWhere("SUBSTRING(b.numero_achat, LENGTH(b.numero_achat) - 3) = :numero_achat")
                    ->setParameter('numero_achat',$form["numero_achat"]->getData());
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

            if ($form["type_marche"]->getData()) {
                $queryBuilder
                    ->andWhere('b.type_marche = :type_marche')
                    ->setParameter('type_marche', $type);
            }


            if ($montantAchatMin && $montantAchatMax) {
                // Cas où les deux valeurs sont fournies
                $queryBuilder
                    ->andWhere('b.montant_achat > :montant_achat_min')
                    ->andWhere('b.montant_achat < :montant_achat_max')
                    ->setParameter('montant_achat_min', $montantAchatMin)
                    ->setParameter('montant_achat_max', $montantAchatMax);
            } elseif ($montantAchatMin) {
                // Cas où seulement montant_achat_min est fourni
                $queryBuilder
                    ->andWhere('b.montant_achat > :montant_achat_min')
                    ->setParameter('montant_achat_min', $montantAchatMin);
            } elseif ($montantAchatMax) {
                // Cas où seulement montant_achat_max est fourni
                $queryBuilder
                    ->andWhere('b.montant_achat < :montant_achat_max')
                    ->setParameter('montant_achat_max', $montantAchatMax);
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
            if ($form["debut_rec"]->getData() && $form["fin_rec"]->getData()) {
                $queryBuilder
                    ->andWhere('b.date_saisie > :debut_rec')
                    ->andWhere('b.date_saisie < :fin_rec')
                    ->setParameter('debut_rec',  $form["debut_rec"]->getData()->format('Y-m-d') )
                    ->setParameter('fin_rec',   $form["fin_rec"]->getData()->format('Y-m-d') );
            }
            elseif($form["debut_rec"]->getData()){
                $queryBuilder
                ->andWhere('b.date_saisie > :debut_rec')
                ->setParameter('debut_rec',  $form["debut_rec"]->getData()->format('Y-m-d') );
            }
            elseif($form["fin_rec"]->getData()){
                $queryBuilder
                ->andWhere('b.date_saisie > :fin_rec')
                ->setParameter('fin_rec',  $form["fin_rec"]->getData()->format('Y-m-d') );
            }
            if ($form["zipcode"]->getData()) {
                // Add a join with the 'fournisseurs' table to filter by 'zipcode'
                $queryBuilder
                    ->join('b.num_siret', 'f') // Assuming 'numSiret' is the association to 'fournisseurs' in your 'achat' entity
                    ->andWhere('f.code_postal = :zipcode')
                    ->setParameter('zipcode', $form["zipcode"]->getData());
            }
            if ($form["id_demande_achat"]->getData()) {
                // Add a join with the 'fournisseurs' table to filter by 'zipcode'
                $queryBuilder
                    ->andWhere('b.id_demande_achat = :id_demande_achat')
                    ->setParameter('id_demande_achat', $form["id_demande_achat"]->getData());
            }
        // ... Votre logique de construction de la requête ici ...
         $queryBuilder->orderBy('b.date_saisie', 'DESC');

        $query = $queryBuilder;
    
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
            $selectFields[] = $form['montant_ht_attr']->getData() == true ? 'b.montant_achat montant_ht' : null;
            $selectFields[] = $form['obs_attr']->getData() == true ? 'b.observations' : null;
            $selectFields[] = $form['etat_achat_attr']->getData() == true ? 'b.etat_achat' : null;
            $selectFields = array_filter($selectFields);

        $queryBuilder
            ->select($selectFields);


           
            $form['code_acheteur_attr']->getData() == true ? $queryBuilder->leftJoin('b.utilisateurs', 'x')->addSelect('x.trigram') : null;
            $form['nom_acheteur_attr']->getData() == true ? $queryBuilder->leftJoin('b.utilisateurs', 'u')->addSelect('u.nom_utilisateur') : null;

            $form['code_formation_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_formation', 'w')->addSelect('w.code_formation') : null;
            $form['libelle_formation_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_formation', 'g')->addSelect('g.libelle_formation') : null;

            $form['code_uo_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_uo', 'y')->addSelect('y.code_uo') : null;
            $form['libelle_uo_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_uo', 'o')->addSelect('o.libelle_uo') : null;

            $form['code_cpv_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_cpv', 'v')->addSelect('v.code_cpv') : null;
            $form['libelle_cpv_attr']->getData() == true ? $queryBuilder->leftJoin('b.code_cpv', 'c')->addSelect('c.libelle_cpv') : null;

            if ($form['tva_attr']->getData() == true ||  $form['montant_ttc_attr'] == true) {

                $queryBuilder->leftJoin('b.tva_ident', 't'); // Jointure avec la table 'service'
                $selectFields[] = $form['tva_attr']->getData() == true ? $queryBuilder->addSelect('t.tva_taux') : null;
                $selectFields[] = $form['montant_ttc_attr']->getData() == true ?  $queryBuilder->addSelect('(b.montant_achat * (t.tva_taux / 100) + b.montant_achat) montant_ttc') : null;
            }

            if (  $form['siret_fournisseur_attr']->getData() == true||  $form['nom_fournisseur_attr']->getData() == true ) {

                $queryBuilder->leftJoin('b.num_siret', 'f'); // Jointure avec la table 'fournisseurs'
    
               

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
            if ($form["debut_rec"]->getData() && $form["fin_rec"]->getData()) {
                $queryBuilder
                    ->andWhere('b.date_saisie > :debut_rec')
                    ->andWhere('b.date_saisie < :fin_rec')
                    ->setParameter('debut_rec',  $form["debut_rec"]->getData()->format('Y-m-d') )
                    ->setParameter('fin_rec',   $form["fin_rec"]->getData()->format('Y-m-d') );
            }
            elseif($form["debut_rec"]->getData()){
                $queryBuilder
                ->andWhere('b.date_saisie > :debut_rec')
                ->setParameter('debut_rec',  $form["debut_rec"]->getData()->format('Y-m-d') );
            }
            elseif($form["fin_rec"]->getData()){
                $queryBuilder
                ->andWhere('b.date_saisie > :fin_rec')
                ->setParameter('fin_rec',  $form["fin_rec"]->getData()->format('Y-m-d') );
            }
        // ... Votre logique de construction de la requête ici ...
        $queryBuilder->orderBy('b.date_saisie', 'DESC')->addOrderBy('s.code_service', 'ASC')->addOrderBy('s.nom_service', 'ASC');

        $query = $queryBuilder->getQuery();
    
        return $query;
    }
    public function getPurchaseCountAndTotalAmount($type, $form)
{
    $data = $form->getData();
    $date = $form["date"]->getData();
    $tax = $form["tax"]->getData();
    $etat_achat = $form["etat_achat"]->getData();
    $anne_precedente = $form["annee_precedente"]->getData();
    
    $qb = $this->createQueryBuilder('a');
    
    // Determine the date column to use
    $dateColumn = ($etat_achat == 'valid') ? 'a.date_notification' : 'a.date_saisie';
    
    // Select statement based on tax type
    if ($tax == 'ht') {
        $qb->select('YEAR(' . $dateColumn . ') AS year, MONTH(' . $dateColumn . ') AS month, COUNT(a) AS count, SUM(a.montant_achat) AS totalmontant');
    } elseif ($tax == 'ttc') {
        $qb->select('YEAR(' . $dateColumn . ') AS year, MONTH(' . $dateColumn . ') AS month, COUNT(a) AS count, SUM(a.montant_achat * (1 + t.tva_taux / 100)) AS totalmontant')
           ->innerJoin('\App\Entity\TVA', 't', Join::WITH, 'a.tva_ident = t.id');
    }
    
    // Basic conditions
    $qb->andWhere('YEAR(' . $dateColumn . ') IN (:years)')
       ->andWhere('a.type_marche = :type_marche')
       ->setParameter('years', [$date, $anne_precedente == 'anneePrecedente' ? $date - 1 : $date])
       ->setParameter('type_marche', $type);
    
    // Add etat_achat condition if it is 'valid'
    if ($etat_achat == 'valid') {
        $qb->andWhere('a.etat_achat = :etat_achats')
           ->setParameter('etat_achats', 2);
    }
    
    // Additional filters
    if ($data->getUtilisateurs()) {
        $qb->andWhere('a.utilisateurs = :utilisateurs')
           ->setParameter('utilisateurs', $data->getUtilisateurs());
    }
    if ($data->getNumSiret()) {
        $qb->andWhere('a.num_siret = :num_siret')
           ->setParameter('num_siret', $data->getNumSiret());
    }
    if ($data->getCodeUo()) {
        $qb->andWhere('a.code_uo = :code_uo')
           ->setParameter('code_uo', $data->getCodeUo());
    }
    if ($data->getCodeCpv()) {
        $qb->andWhere('a.code_cpv = :code_cpv')
           ->setParameter('code_cpv', $data->getCodeCpv());
    }
    if ($data->getCodeFormation()) {
        $qb->andWhere('a.code_formation = :code_formation')
           ->setParameter('code_formation', $data->getCodeFormation());
    }
    
    // Group by year and month
    $result = $qb->groupBy('year, month')
                 ->getQuery()
                 ->getResult();
    
    // Organize results into a structured array
    $organizedResults = [
        'current_year' => [],
        'previous_year' => []
    ];

    foreach ($result as $row) {
        if ($row['year'] == $date) {
            $organizedResults['current_year'][] = $row;
        } elseif ($row['year'] == ($date - 1)) {
            $organizedResults['previous_year'][] = $row;
        }
    }
    return $organizedResults;
}
public function getTotalAchatUnder2K($form)
{
    // Retrieve data from the form correctly
    $data = $form->getData();
    $date = $form->get('date')->getData();
    $tax = $form->get('tax')->getData();
    $etat_achat = $form->get('etat_achat')->getData();
    $annee_precedente = $form->get('annee_precedente')->getData();
    
    $qb = $this->createQueryBuilder('a');
    
    // Determine the date column to use
    $dateColumn = ($etat_achat == 'valid') ? 'a.date_notification' : 'a.date_saisie';
    
    // Join the TVA table
    $qb->innerJoin('a.tva_ident', 't');
    
    // New query to count purchases with montant_achat less than 2000 (taking into account the tva)
    $qb->select('a.type_marche, YEAR(' . $dateColumn . ') AS year, MONTH(' . $dateColumn . ') AS month, COUNT(a) AS count')
       ->where('a.montant_achat * (1 + t.tva_taux / 100) < 2000');
    
    // Basic conditions
    $qb->andWhere('YEAR(' . $dateColumn . ') IN (:years)')
       ->andWhere('a.etat_achat = :etat_achats')
       ->setParameter('years', [$date, $annee_precedente == 'anneePrecedente' ? $date - 1 : $date])
       ->setParameter('etat_achats', 2);
    
    // Additional filters
    if ($data->getUtilisateurs()) {
        $qb->andWhere('a.utilisateurs = :utilisateurs')
           ->setParameter('utilisateurs', $data->getUtilisateurs());
    }
    if ($data->getNumSiret()) {
        $qb->andWhere('a.num_siret = :num_siret')
           ->setParameter('num_siret', $data->getNumSiret());
    }
    if ($data->getCodeUo()) {
        $qb->andWhere('a.code_uo = :code_uo')
           ->setParameter('code_uo', $data->getCodeUo());
    }
    if ($data->getCodeCpv()) {
        $qb->andWhere('a.code_cpv = :code_cpv')
           ->setParameter('code_cpv', $data->getCodeCpv());
    }
    if ($data->getCodeFormation()) {
        $qb->andWhere('a.code_formation = :code_formation')
           ->setParameter('code_formation', $data->getCodeFormation());
    }
    
    // Group by type_marche, year, and month
    $result = $qb->groupBy('a.type_marche, year, month')
                 ->getQuery()
                 ->getResult();
    
    // Organize results into a structured array
    $organizedResults = [
        'type_marche_1' => [
            'current_year' => [],
            'previous_year' => []
        ],
        'type_marche_0' => [
            'current_year' => [],
            'previous_year' => []
        ]
    ];

    foreach ($result as $row) {
        if ($row['type_marche'] == 1) {
            if ($row['year'] == $date) {
                $organizedResults['type_marche_1']['current_year'][] = $row;
            } elseif ($row['year'] == ($date - 1)) {
                $organizedResults['type_marche_1']['previous_year'][] = $row;
            }
        } elseif ($row['type_marche'] == 0) {
            if ($row['year'] == $date) {
                $organizedResults['type_marche_0']['current_year'][] = $row;
            } elseif ($row['year'] == ($date - 1)) {
                $organizedResults['type_marche_0']['previous_year'][] = $row;
            }
        }
    }
    // dd($organizedResults);
    return $organizedResults;
}
    
   
public function getYearDelayDiff($form)
{
    $criteria = $this->extractCriteriaFromForm($form);

    $jourcalendar = $form["jourcalendar"]->getData();

    $conn = $this->getEntityManager()->getConnection();
    if ($jourcalendar == "jO") {
        $sql = "
        SELECT
        source,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 1 THEN difference END), 2) AS Mois_1,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 2 THEN difference END), 2) AS Mois_2,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 3 THEN difference END), 2) AS Mois_3,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 4 THEN difference END), 2) AS Mois_4,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 5 THEN difference END), 2) AS Mois_5,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 6 THEN difference END), 2) AS Mois_6,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 7 THEN difference END), 2) AS Mois_7,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 8 THEN difference END), 2) AS Mois_8,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 9 THEN difference END), 2) AS Mois_9,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 10 THEN difference END), 2) AS Mois_10,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 11 THEN difference END), 2) AS Mois_11,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 12 THEN difference END), 2) AS Mois_12
        FROM (
            SELECT
                'ANT GSBDD' AS source,
                (DATEDIFF(date_commande_chorus, date_sillage) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_commande_chorus)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'BUDGET' AS source,
                (DATEDIFF(date_valid_inter, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_valid_inter)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'APPRO' AS source,
                (DATEDIFF(date_validation, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_valid_inter AND date_validation)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'FIN' AS source,
                (DATEDIFF(date_notification, date_validation) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_validation AND date_notification)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'PFAF' AS source,
                (DATEDIFF(date_notification, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_notification)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'Chorus formul.' AS source,
                (DATEDIFF(date_notification, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_notification)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
        ) AS combined_data
        GROUP BY source
        ORDER BY
            source = 'ANT GSBDD' DESC,
            source = 'BUDGET' DESC,
            source = 'APPRO' DESC,
            source = 'FIN' DESC,
            source = 'PFAF' DESC,
            source = 'Chorus formul.' DESC
        LIMIT 0, 100
        ";
    } else {
        $sql = "
        SELECT
        source,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 1 THEN difference END), 2) AS Mois_1,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 2 THEN difference END), 2) AS Mois_2,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 3 THEN difference END), 2) AS Mois_3,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 4 THEN difference END), 2) AS Mois_4,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 5 THEN difference END), 2) AS Mois_5,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 6 THEN difference END), 2) AS Mois_6,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 7 THEN difference END), 2) AS Mois_7,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 8 THEN difference END), 2) AS Mois_8,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 9 THEN difference END), 2) AS Mois_9,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 10 THEN difference END), 2) AS Mois_10,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 11 THEN difference END), 2) AS Mois_11,
        ROUND(AVG(CASE WHEN MONTH(date_notification) = 12 THEN difference END), 2) AS Mois_12
        FROM (
            SELECT
                'ANT GSBDD' AS source,
                (DATEDIFF(date_commande_chorus, date_sillage)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'BUDGET' AS source,
                (DATEDIFF(date_valid_inter, date_commande_chorus)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'APPRO' AS source,
                (DATEDIFF(date_validation, date_valid_inter)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'FIN' AS source,
                (DATEDIFF(date_notification, date_validation)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'PFAF' AS source,
                (DATEDIFF(date_notification, date_valid_inter)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
            UNION ALL
            SELECT
                'Chorus formul.' AS source,
                (DATEDIFF(date_notification, date_commande_chorus)) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
        ) AS combined_data
        GROUP BY source
        ORDER BY
            source = 'ANT GSBDD' DESC,
            source = 'BUDGET' DESC,
            source = 'APPRO' DESC,
            source = 'FIN' DESC,
            source = 'PFAF' DESC,
            source = 'Chorus formul.' DESC
        LIMIT 0, 100
        ";
    }
    $stmt = $conn->prepare($sql);

    $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
    $achats = $resultSet->fetchAllAssociative();

    return $achats;
}       
        public function getYearDelayCount($form)
        {
            $criteria = $this->extractCriteriaFromForm($form);

            $jourcalendar = $form["jourcalendar"]->getData();

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
                            (DATEDIFF(date_commande_chorus, date_sillage) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_commande_chorus )) AS difference,
                            date_notification
                        FROM achat
                        WHERE YEAR(date_notification) = :year AND etat_achat = 2
                        {$criteria['conditions']}
        
                UNION ALL

                SELECT
                'BUDGET' AS source,
                (DATEDIFF(date_valid_inter, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_valid_inter )) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
        
                UNION ALL
            
                SELECT
                    'APPRO' AS source,
                    (DATEDIFF(date_validation, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_valid_inter AND date_validation )) AS difference,
                    date_notification
                FROM achat
                WHERE YEAR(date_notification) = :year AND etat_achat = 2
                {$criteria['conditions']}
        
                UNION ALL
                SELECT
                'FIN' AS source,
                (DATEDIFF(date_notification, date_validation) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_validation AND date_notification  )) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
        
                UNION ALL
            
                SELECT
                    'PFAF' AS source,
                    (DATEDIFF(date_notification, date_valid_inter) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_valid_inter AND date_notification  )) AS difference,
                    date_notification
                FROM achat
                WHERE YEAR(date_notification) = :year AND etat_achat = 2
                {$criteria['conditions']}
        
                UNION ALL
            
                SELECT
                'Chorus formul.' AS source,
                (DATEDIFF(date_notification, date_commande_chorus) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_commande_chorus AND date_notification )) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}

                UNION ALL
            
                SELECT
                'Délai total' AS source,
                (DATEDIFF(date_notification, date_sillage) - (SELECT COUNT(*) FROM calendar WHERE start BETWEEN date_sillage AND date_notification )) AS difference,
                date_notification
            FROM achat
            WHERE YEAR(date_notification) = :year AND etat_achat = 2
            {$criteria['conditions']}
                
        
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
                          date_notification
                        FROM achat
                        WHERE YEAR(date_notification) = :year AND etat_achat = 2
                        {$criteria['conditions']}
                
                        UNION ALL
                      
                        SELECT
                          'BUDGET' AS source,
                          DATEDIFF(date_valid_inter, date_commande_chorus) AS difference,
                          date_notification
                        FROM achat
                        WHERE YEAR(date_notification) = :year AND etat_achat = 2
                        {$criteria['conditions']}
                
                        UNION ALL
                      
                        SELECT
                          'APPRO' AS source,
                          DATEDIFF(date_validation, date_valid_inter) AS difference,
                          date_notification
                        FROM achat
                        WHERE YEAR(date_notification) = :year AND etat_achat = 2
                        {$criteria['conditions']}
                
                        UNION ALL
                      
                        SELECT
                          'FIN' AS source,
                          DATEDIFF(date_notification, date_validation) AS difference,
                          date_notification
                        FROM achat
                        WHERE YEAR(date_notification) = :year AND etat_achat = 2
                        {$criteria['conditions']}
                
                        UNION ALL
                      
                        SELECT
                          'PFAF' AS source,
                          DATEDIFF(date_notification, date_valid_inter) AS difference,
                          date_notification
                        FROM achat
                        WHERE YEAR(date_notification) = :year AND etat_achat = 2
                        {$criteria['conditions']}
                
                        UNION ALL
                      
                        SELECT
                          'Chorus formul.' AS source,
                          DATEDIFF(date_notification, date_commande_chorus) AS difference,
                          date_notification
                        FROM achat
                        WHERE YEAR(date_notification) = :year AND etat_achat = 2
                        {$criteria['conditions']}

                        UNION ALL
            
                        SELECT
                        'Délai total' AS source,
                        (DATEDIFF(date_notification, date_sillage)) AS difference,
                        date_notification
                    FROM achat
                    WHERE YEAR(date_notification) = :year AND etat_achat = 2
                    {$criteria['conditions']}
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
      
                $resultSet = $conn->executeQuery($sql, array_merge(['year' => $criteria['date']], $criteria['parameters']));
           
                $achats=$resultSet->fetchAllAssociative();
               
                // dd($achats);
                return $achats;
            }

            public function getVolValDelay($form)
            {
                $criteria = $this->extractCriteriaFromForm($form);
            
                $conn = $this->getEntityManager()->getConnection();
            
                $sql = "
                    WITH Mois AS (
                        SELECT 1 AS Mois UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL 
                        SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL 
                        SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL 
                        SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
                    ), 
                    Calculs AS (
                        SELECT 
                            MONTH(a.date_notification) AS MoisNotif, 
                            DATEDIFF(a.date_notification, a.date_valid_inter) AS DiffJours
                        FROM 
                            achat a
                        WHERE 
                            YEAR(date_notification) = :year AND etat_achat = 2
                            {$criteria['conditions']}
                    )
                    SELECT
                        m.Mois,
                        SUM(CASE WHEN c.DiffJours <= 15 THEN 1 ELSE 0 END) AS 'Achats <= 15 jours',
                        SUM(CASE WHEN c.DiffJours BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS 'Achats 16-30 jours',
                        SUM(CASE WHEN c.DiffJours > 30 THEN 1 ELSE 0 END) AS 'Achats > 30 jours'
                    FROM
                        Mois m
                    LEFT JOIN
                        Calculs c ON m.Mois = c.MoisNotif
                    GROUP BY
                        m.Mois
                    LIMIT 100";
            
                $stmt = $conn->prepare($sql);
            
                $parameters = array_merge(['year' => $criteria['date']], $criteria['parameters']);
                $resultSet = $conn->executeQuery($sql, $parameters);
            
                $achats = $resultSet->fetchAllAssociative();
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
            $numeroAchat = $this->achatNumberService->generateAchatNumber();
            $achat->setNumeroAchat($numeroAchat);
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