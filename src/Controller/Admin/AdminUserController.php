<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Role;
use App\Form\AdminEditType;
use App\Form\AdminCreateType;
use App\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin/admin/user')]

class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_admin_user_index', methods: ['GET'])]

    public function index(AdminRepository $adminRepository): Response
    {
        return $this->render('admin/admin_user/index.html.twig', [
            'admins' => $adminRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_admin_user_new', methods: ['GET','POST'])]

    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminCreateType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            //actualizacion usuario
            $password = $encoder->encodePassword($admin, $form['password']->getData());
            $admin->setPassword($password);

            //actualizacion de los roles
            $arrayRole = $form['roles_in_form']->getData();
            $roles = $entityManager->getRepository(Role::class)->findAll();
            //consulto si existen para agregar el rol
            foreach($roles as $role){
                if(in_array($role->getId(), $arrayRole)){
                    $admin->addRole($role);
                }
            }
            
            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('admin_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_user/new.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_admin_user_show', methods: ['GET'])]
    public function show(Admin $admin): Response
    {
        return $this->render('admin/admin_user/show.html.twig', [
            'admin' => $admin,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_admin_user_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Admin $admin, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(AdminCreateType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            if(!empty($form['password']->getData())){
                $password = $encoder->encodePassword($admin, $form['password']->getData());
                $admin->setPassword($password);
            }

            //actualizacion de los roles
            $arrayRole = $form['roles_in_form']->getData();
            $roles = $entityManager->getRepository(Role::class)->findAll();
            //consulto si existen para agregar el rol y si tenía lo remueve para editarlo
            foreach($roles as $role){
                if(in_array($role->getId(), $arrayRole)){
                    $admin->addRole($role);
                }else{
                    $admin->removeRole($role);
                }
            }
            //el flush siempre va al final para actualizar los cambios
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_user/edit.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, Admin $admin): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admin->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($admin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
