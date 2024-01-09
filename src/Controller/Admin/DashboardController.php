<?php

namespace App\Controller\Admin;

use App\Entity\UO;
use App\Entity\CPV;
use App\Entity\Achat;
use App\Entity\Services;
use App\Entity\Formations;
use App\Entity\Fournisseurs;
use App\Entity\Utilisateurs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator) 
        {
    }
    #[Route('/fournisseursadmin', name: 'fournisseursadmin')]
    public function fournisseurs(): Response
{
    $url = $this->adminUrlGenerator
        ->setController(FournisseursCrudController::class)
        ->generateUrl(Action::INDEX);

    return $this->redirect($url);
}

#[Route('/cpvadmin', name: 'cpvadmin')]
public function cpv(): Response
{
$url = $this->adminUrlGenerator
    ->setController(CPVCrudController::class)
    ->generateUrl(Action::INDEX);

return $this->redirect($url);
}

#[Route('/UOadmin', name: 'UOadmin')]
public function UO(): Response
{
$url = $this->adminUrlGenerator
    ->setController(UOCrudController::class)
    ->generateUrl(Action::INDEX);

return $this->redirect($url);
}
#[Route('/formationsadmin', name: 'formationsadmin')]
public function formations(): Response
{
$url = $this->adminUrlGenerator
    ->setController(FormationsCrudController::class)
    ->generateUrl(Action::INDEX);

return $this->redirect($url);
}
#[Route('/serviceadmin', name: 'serviceadmin')]
public function service(): Response
{
$url = $this->adminUrlGenerator
    ->setController(ServicesCrudController::class)
    ->generateUrl(Action::INDEX);

return $this->redirect($url);
}
#[Route('/utilisateursadmin', name: 'utilisateursadmin')]
public function utilisateurs(): Response
{
$url = $this->adminUrlGenerator
    ->setController(UtilisateursCrudController::class)
    ->generateUrl(Action::INDEX);

return $this->redirect($url);
}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $url = $this->adminUrlGenerator
        ->setController(UtilisateursCrudController::class)
        ->generateUrl();
        return $this->redirect($url);
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
/*          $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class)
                     ->setController(UtilisateursCrudController::class)
                     ->generateUrl();
                     return $this->redirect($adminUrlGenerator) */

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }
    // #[Route('/achat', name: 'achat')]
    // public function achat(): Response
    // {

    //     $url = $this->adminUrlGenerator
    //     ->setController(UtilisateursCrudController::class)
    //     ->setController(ServicesCrudController::class)
    //     ->setController(FournisseursCrudController::class)
    //     ->setController(UOCrudController::class)
    //     ->setController(FormationsCrudController::class)
    //     ->setController(CPVCrudController::class)
    //     ->generateUrl();
    //     return $this->redirect($url);

    // }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Suivi des achats');

    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute("Retour vers la page d'accueil", 'fas fa-home', 'app_login');

        if ($this->isGranted('ROLE_OPT_SERVICES')){        
        yield MenuItem::subMenu('Utilisateurs', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer un utilisateur','fas fa-plus', Utilisateurs::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les utilisateurs','fas fa-eye', Utilisateurs::class)
        ]);
    }
        if ($this->isGranted('ROLE_OPT_SERVICES')){        
        yield MenuItem::subMenu('Service', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer un service','fas fa-plus', Services::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les services','fas fa-eye', Services::class)
        ]);
    }
        if ($this->isGranted('ROLE_OPT_FOURNISSEURS')){        
        yield MenuItem::subMenu('Fournisseurs', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer un fournisseur','fas fa-plus', Fournisseurs::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les fournisseurs','fas fa-eye', Fournisseurs::class)
        ]);
    }
    if ($this->isGranted('ROLE_OPT_UO')){        
        yield MenuItem::subMenu('UO', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer un UO','fas fa-plus', UO::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les UOs','fas fa-eye', UO::class)
        ]);
    }
        if ($this->isGranted('ROLE_OPT_FORMATIONS')){        
        yield MenuItem::subMenu('Formations', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer une formation','fas fa-plus', Formations::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les formations','fas fa-eye', Formations::class)
        ]);
    }
        if ($this->isGranted('ROLE_OPT_CPV')){
        yield MenuItem::subMenu('CPV', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer un CPV','fas fa-plus', CPV::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les CPVs','fas fa-eye', CPV::class)
            ->setPermission('ROLE_OPT_CPV'),

        ]);  
          }  
        }
    
}
