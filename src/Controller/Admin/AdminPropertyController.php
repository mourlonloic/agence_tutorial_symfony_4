<?php
namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPropertyController extends AbstractController
{
    private $repository;
    private $om;

    public function __construct(PropertyRepository $repository, ObjectManager $om)
    {
        $this->repository = $repository;
        $this->om = $om;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render("admin/property/index.html.twig", compact("properties"));
        
    }

    /**
     * @Route("/admin/property/create", name="admin.property.new")
     */
    public function new(Request $request)
    {
        $property = new Property;
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->om->persist($property);
            $this->om->flush();
            $this->addFlash("success", "Le bien a été créé avec succès");

            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/new.html.twig', [
            "property" => $property,
            "form" => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST") 
     */
    public function edit(Property $property, Request $request)
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->om->flush();
            $this->addFlash("success", "Le bien a été modifié avec succès");
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig', [
            "property" => $property,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE") 
     */
    public function delete(Property $property, Request $request)
    {
        if($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token')))
        {
            $this->om->remove($property);
            $this->om->flush();
            $this->addFlash("success", "Le bien a été supprimé avec succès");
        }
        return $this->redirectToRoute('admin.property.index');
    }
}
