<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Organizacion;
use App\Form\RegistrationFormType;
use App\Repository\CursoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//TODO: Agregar campo para registrar como docente o responsable

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $em;
    private CursoRepository $cr;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->er = $this->em->getRepository(Organizacion::class);
    }

    #[Route('/registro/{tipousuario}', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, string $tipousuario = 'docente'): Response
    {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            //HACK: set all users to DOCENTE
            $roles = ['ROLE_USER'];
            $roles[] = 'ROLE_DOCENTE';
            $user->setRoles($roles);

            //Add fisrt organization
            $organizacionid = $request->request->all()['registration_form']['organizacion'];
            $organizacion = $this->er->find($organizacionid);
            if (is_null($organizacion))
                throw new AccessDeniedHttpException();

            $user->addOrganizacion($organizacion);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ], $response);
    }
}
