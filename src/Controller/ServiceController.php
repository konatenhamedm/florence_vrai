<?php

namespace App\Controller;

use App\Repository\TableInfoTrait;
use App\Service\Services;
use App\Entity\Service;
use App\Service\FormError;
use App\Form\ServiceType;
use App\Service\ActionRender;
use App\Repository\ServiceRepository;
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
 * il s'agit du service des module
 */
class ServiceController extends AbstractController
{
    use TableInfoTrait;
    /**
     * @Route("/service/{id}/confirmation", name="service_confirmation", methods={"GET"})
     * @param $id
     * @param Service $parent
     * @return Response
     */
    public function confirmation($id,Service $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'service',
        ]);
    }

    /**
     * @Route("/service", name="service")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          ServiceRepository $serviceRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $serviceRepository->countAll();
        $totalFilteredData = $serviceRepository->countAll($searchValue);
        $data = $serviceRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;


        $table->add('titre', TextColumn::class, ['label' => 'Titre', 'className' => 'w-100px']);
        $table->add('icone', TextColumn::class, ['label' => 'Icon', 'className' => 'w-100px']);
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
                                'url' => $this->generateUrl('service_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('service_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                         /*   'delete' => [
                                'url' => $this->generateUrl('service_delete', ['id' => $value])
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

        return $this->render('_admin/service/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des services']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/service/new", name="service_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request,UploaderHelper $uploaderHelper, FormError $formError, EntityManagerInterface  $em): Response
    {


        $service = new Service();
        $form = $this->createForm(ServiceType::class,$service, [
            'method' => 'POST',
            'action' => $this->generateUrl('service_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {

            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('service');
           // $uploadedFile = $form['image']->getData();
           //dd($format);
            if ($form->isValid()) {


//dd($uploadedFile);
            /*    if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $service->set($newFilename);
                }*/

                $em->persist($service);
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

        return $this->render('_admin/service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
            'titre' => 'Service',
        ]);
    }

    /**
     * @Route("/service/{id}/edit", name="service_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Service $service
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request,UploaderHelper $uploaderHelper, FormError $formError, Service $service, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ServiceType::class,$service, [
            'method' => 'POST',
            'action' => $this->generateUrl('service_edit',[
                'id'=>$service->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('service');
            //$uploadedFile = $form['image']->getData();
            if($form->isValid()){
               /* if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $service->setImage($newFilename);
                }*/

                $em->persist($service);
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

        return $this->render('_admin/service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
            'titre' => 'Service',
        ]);
    }

    /**
     * @Route("/service/{id}/show", name="service_show", methods={"GET"})
     * @param service $service
     * @return Response
     */
    public function show(service $service): Response
    {
        $form = $this->createForm(ServiceType::class,$service, [
            'method' => 'POST',
            'action' => $this->generateUrl('service_show',[
                'id'=>$service->getId(),
            ])
        ]);

        return $this->render('_admin/service/voir.html.twig', [
            'service' => $service,
            'titre' => 'Service',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/service/{id}/active", name="service_active", methods={"GET"})
     * @param $id
     * @param Service $service
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Service $service, EntityManagerInterface $entityManager): Response
    {

        if ($service->getActive() == 1){

            $service->setActive(0);

        }else{

            $service->setActive(1);

        }
        $entityManager->persist($service);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$service->getActive(),
        ],200);


    }


    /**
     * @Route("/service/{id}/delete", name="service_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Service $service
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Service $service): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'service_delete'
                    ,   [
                        'id' => $service->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($service);
            $em->flush();

            $redirect = $this->generateUrl('service');

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
        return $this->render('_admin/service/delete.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

}
