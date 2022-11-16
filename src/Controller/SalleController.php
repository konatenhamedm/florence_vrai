<?php

namespace App\Controller;

use App\Repository\TableInfoTrait;
use App\Service\Services;
use App\Entity\Salle;
use App\Service\FormError;
use App\Form\SalleType;
use App\Service\ActionRender;
use App\Repository\SalleRepository;
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
 * il s'agit du salle des module
 */
class SalleController extends AbstractController
{
    use TableInfoTrait;
    /**
     * @Route("/salle/{id}/confirmation", name="salle_confirmation", methods={"GET"})
     * @param $id
     * @param Salle $parent
     * @return Response
     */
    public function confirmation($id,Salle $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'salle',
        ]);
    }

    /**
     * @Route("/salle", name="salle")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param SalleRepository $salleRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          SalleRepository $salleRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $salleRepository->countAll();
        $totalFilteredData = $salleRepository->countAll($searchValue);
        $data = $salleRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;


        $table->add('titre', TextColumn::class, ['label' => 'Titre', 'className' => 'w-100px']);

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
                                'url' => $this->generateUrl('salle_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('salle_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                         /*   'delete' => [
                                'url' => $this->generateUrl('salle_delete', ['id' => $value])
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

        return $this->render('_admin/salle/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des salles']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/salle/new", name="salle_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request,UploaderHelper $uploaderHelper, FormError $formError, EntityManagerInterface  $em): Response
    {


        $salle = new Salle();
        $form = $this->createForm(SalleType::class,$salle, [
            'method' => 'POST',
            'action' => $this->generateUrl('salle_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {

            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('salle');
            $uploadedFile = $form['image']->getData();
           //dd($format);
            if ($form->isValid()) {


//dd($uploadedFile);
                if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $salle->setImage($newFilename);
                }

                $em->persist($salle);
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

        return $this->render('_admin/salle/new.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
            'titre' => 'Salle',
        ]);
    }

    /**
     * @Route("/salle/{id}/edit", name="salle_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Salle $salle
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request,UploaderHelper $uploaderHelper, FormError $formError, Salle $salle, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(SalleType::class,$salle, [
            'method' => 'POST',
            'action' => $this->generateUrl('salle_edit',[
                'id'=>$salle->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('salle');
            $uploadedFile = $form['image']->getData();
            if($form->isValid()){
                if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $salle->setImage($newFilename);
                }

                $em->persist($salle);
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

        return $this->render('_admin/salle/edit.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
            'titre' => 'Salle',
        ]);
    }

    /**
     * @Route("/salle/{id}/show", name="salle_show", methods={"GET"})
     * @param salle $salle
     * @return Response
     */
    public function show(salle $salle): Response
    {
        $form = $this->createForm(SalleType::class,$salle, [
            'method' => 'POST',
            'action' => $this->generateUrl('salle_show',[
                'id'=>$salle->getId(),
            ])
        ]);

        return $this->render('_admin/salle/voir.html.twig', [
            'salle' => $salle,
            'titre' => 'Salle',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/salle/{id}/active", name="salle_active", methods={"GET"})
     * @param $id
     * @param Salle $salle
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Salle $salle, EntityManagerInterface $entityManager): Response
    {

        if ($salle->getActive() == 1){

            $salle->setActive(0);

        }else{

            $salle->setActive(1);

        }
        $entityManager->persist($salle);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$salle->getActive(),
        ],200);


    }


    /**
     * @Route("/salle/{id}/delete", name="salle_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Salle $salle
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Salle $salle): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'salle_delete'
                    ,   [
                        'id' => $salle->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($salle);
            $em->flush();

            $redirect = $this->generateUrl('salle');

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
        return $this->render('_admin/salle/delete.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }

}
