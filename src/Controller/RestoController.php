<?php

namespace App\Controller;

use App\Repository\TableInfoTrait;
use App\Entity\Resto;
use App\Service\FormError;
use App\Form\RestoType;
use App\Service\ActionRender;
use App\Repository\RestoRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Omines\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\Request;
use Omines\DataTablesBundle\Column\TextColumn;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 * il s'agit du resto des module
 */
class RestoController extends AbstractController
{
    use TableInfoTrait;
    /**
     * @Route("/resto/{id}/confirmation", name="resto_confirmation", methods={"GET"})
     * @param $id
     * @param Resto $parent
     * @return Response
     */
    public function confirmation($id,Resto $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'resto',
        ]);
    }

    /**
     * @Route("/resto", name="resto")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param RestoRepository $restoRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          RestoRepository $restoRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $restoRepository->countAll();
        $totalFilteredData = $restoRepository->countAll($searchValue);
        $data = $restoRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;


        $table->add('titre', TextColumn::class, ['label' => 'Titre', 'className' => 'w-100px']);
        $table->add('description', TextColumn::class, ['label' => 'Description', 'className' => 'w-100px']);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            /*    'suivi' =>  new ActionRender(function () use ($etat) {
                    return in_array($etat, ['cree']);
                }),*/
            'delete' => new ActionRender(function (){
                return true;
            }),
            'details' => new ActionRender(function () {
                return true;
            }),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }


        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions'
                , 'field' => 'id'
                , 'orderable' => false
                ,'globalSearchable' => false
                ,'className' => 'grid_row_actions'
                , 'render' => function ($value, $context) use ($renders) {

                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#extralargemodal1',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('resto_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('resto_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                         /*   'delete' => [
                                'url' => $this->generateUrl('resto_delete', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-trash-2'
                                , 'attrs' => ['class' => 'btn-danger', 'title' => 'Suppression']
                                , 'target' => '#smallmodal'
                                ,  'render' => new ActionRender(function () use ($renders) {
                                    return $renders['delete'];
                                })
                            ],*/
                        ]
                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('_admin/resto/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des restos']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/resto/new", name="resto_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request,UploaderHelper $uploaderHelper, FormError $formError, EntityManagerInterface  $em): Response
    {


        $resto = new Resto();
        $form = $this->createForm(RestoType::class,$resto, [
            'method' => 'POST',
            'action' => $this->generateUrl('resto_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {

            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('resto');
            $uploadedFile = $form['image']->getData();
            $uploadedFile1 = $form['fichier']->getData();
           //dd($format);
            if ($form->isValid()) {


//dd($uploadedFile);
               if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $resto->setImage($newFilename);
                }
                if ($uploadedFile1) {
                    $newFilename1 = $uploaderHelper->uploadImage($uploadedFile1);
                    $resto->setFichier($newFilename1);
                }

                $em->persist($resto);
                $em->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }
            }


            /*  }*/
            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }

        if ($isAjax) {
            return $this->json(compact('statut', 'message', 'redirect', 'data'));
        } else {
            if ($statut == 1) {
                return $this->redirect($redirect);
            }
        }
        }

        return $this->render('_admin/resto/new.html.twig', [
            'resto' => $resto,
            'form' => $form->createView(),
            'titre' => 'Resto',
        ]);
    }

    /**
     * @Route("/resto/{id}/edit", name="resto_edit", methods={"GET","POST"})
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     * @param FormError $formError
     * @param Resto $resto
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request,UploaderHelper $uploaderHelper, FormError $formError, Resto $resto, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(RestoType::class,$resto, [
            'method' => 'POST',
            'action' => $this->generateUrl('resto_edit',[
                'id'=>$resto->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('resto');
            $uploadedFile = $form['image']->getData();
            $uploadedFile1 = $form['fichier']->getData();
            if($form->isValid()){
                if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $resto->setImage($newFilename);
                }
                if ($uploadedFile1) {
                    $newFilename1 = $uploaderHelper->uploadImage($uploadedFile1);
                    $resto->setFichier($newFilename1);
                }


                $em->persist($resto);
                $em->flush();

                $message       = 'Opération effectuée avec succès';
                $data = true;
                $this->addFlash('success', $message);

            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }
            }

            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('_admin/resto/edit.html.twig', [
            'resto' => $resto,
            'form' => $form->createView(),
            'titre' => 'Resto',
        ]);
    }

    /**
     * @Route("/resto/{id}/show", name="resto_show", methods={"GET"})
     * @param resto $resto
     * @return Response
     */
    public function show(resto $resto): Response
    {
        $form = $this->createForm(RestoType::class,$resto, [
            'method' => 'POST',
            'action' => $this->generateUrl('resto_show',[
                'id'=>$resto->getId(),
            ])
        ]);

        return $this->render('_admin/resto/voir.html.twig', [
            'resto' => $resto,
            'titre' => 'Resto',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/resto/{id}/active", name="resto_active", methods={"GET"})
     * @param $id
     * @param Resto $resto
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Resto $resto, EntityManagerInterface $entityManager): Response
    {

        if ($resto->getActive() == 1){

            $resto->setActive(0);

        }else{

            $resto->setActive(1);

        }
        $entityManager->persist($resto);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$resto->getActive(),
        ],200);


    }


    /**
     * @Route("/resto/{id}/delete", name="resto_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Resto $resto
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Resto $resto): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'resto_delete'
                    ,   [
                        'id' => $resto->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($resto);
            $em->flush();

            $redirect = $this->generateUrl('resto');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => true
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }



        }
        return $this->render('_admin/resto/delete.html.twig', [
            'resto' => $resto,
            'form' => $form->createView(),
        ]);
    }

}
