<?php

namespace App\Controller;

use App\Repository\TableInfoTrait;
use App\Service\Services;
use App\Entity\Chambre;
use App\Service\FormError;
use App\Form\ChambreType;
use App\Service\ActionRender;
use App\Repository\ChambreRepository;
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
 * il s'agit du chambre des module
 */
class ChambreController extends AbstractController
{
    use TableInfoTrait;
    /**
     * @Route("/chambre/{id}/confirmation", name="chambre_confirmation", methods={"GET"})
     * @param $id
     * @param Chambre $parent
     * @return Response
     */
    public function confirmation($id,Chambre $parent): Response
    {
        return $this->render('_admin/modal/confirmation.html.twig',[
            'id'=>$id,
            'action'=>'chambre',
        ]);
    }

    /**
     * @Route("/chambre", name="chambre")
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     * @param ChambreRepository $chambreRepository
     * @return Response
     */
    public function index(Request $request,
                          DataTableFactory $dataTableFactory,
                          ChambreRepository $chambreRepository): Response
    {

        $table = $dataTableFactory->create();

        $user = $this->getUser();

        $requestData = $request->request->all();

        $offset = intval($requestData['start'] ?? 0);
        $limit = intval($requestData['length'] ?? 10);

        $searchValue = $requestData['search']['value'] ?? null;



        $totalData = $chambreRepository->countAll();
        $totalFilteredData = $chambreRepository->countAll($searchValue);
        $data = $chambreRepository->getAll($limit, $offset,  $searchValue);

//dd($data);


        $table->createAdapter(ArrayAdapter::class, [
            'data' => $data,
            'totalRows' => $totalData,
            'totalFilteredRows' => $totalFilteredData
        ]) ->setName('dt_');
        ;


        $table->add('libelle', TextColumn::class, ['label' => 'Libelle', 'className' => 'w-100px']);
        $table->add('prix', NumberColumn::class, ['label' => 'Prix', 'className' => 'w-100px']);

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
                                'url' => $this->generateUrl('chambre_edit', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-edit'
                                , 'attrs' => ['class' => 'btn-success']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['edit'];
                                })
                            ],
                            'details' => [
                                'url' => $this->generateUrl('chambre_show', ['id' => $value])
                                , 'ajax' => true
                                , 'icon' => '%icon% fe fe-eye'
                                , 'attrs' => ['class' => 'btn-primary']
                                , 'render' => new ActionRender(function () use ($renders) {
                                    return $renders['details'];
                                })
                            ],
                         /*   'delete' => [
                                'url' => $this->generateUrl('chambre_delete', ['id' => $value])
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

        return $this->render('_admin/chambre/index.html.twig', ['datatable' => $table, 'titre' => 'Liste des chambres']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security->getUser()->getUserIdentifier();

    }

    /**
     * @Route("/chambre/new", name="chambre_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request,UploaderHelper $uploaderHelper, FormError $formError, EntityManagerInterface  $em): Response
    {


        $chambre = new Chambre();
        $form = $this->createForm(ChambreType::class,$chambre, [
            'method' => 'POST',
            'action' => $this->generateUrl('chambre_new')
        ]);

        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {

            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('chambre');
            $uploadedFile = $form['image']->getData();
           //dd($format);
            if ($form->isValid()) {


//dd($uploadedFile);
                if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $chambre->setImage($newFilename);
                }

                $em->persist($chambre);
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

        return $this->render('_admin/chambre/new.html.twig', [
            'chambre' => $chambre,
            'form' => $form->createView(),
            'titre' => 'Chambre',
        ]);
    }

    /**
     * @Route("/chambre/{id}/edit", name="chambre_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Chambre $chambre
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Request $request,UploaderHelper $uploaderHelper, FormError $formError, Chambre $chambre, EntityManagerInterface  $em): Response
    {

        $form = $this->createForm(ChambreType::class,$chambre, [
            'method' => 'POST',
            'action' => $this->generateUrl('chambre_edit',[
                'id'=>$chambre->getId(),
            ])
        ]);
        $form->handleRequest($request);
        $data = null;
        $isAjax = $request->isXmlHttpRequest();

        if($form->isSubmitted())
        {
            $statut = 1;
            $response = [];
            $redirect = $this->generateUrl('chambre');
            $uploadedFile = $form['image']->getData();
            if($form->isValid()){
                if ($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                    $chambre->setImage($newFilename);
                }

                $em->persist($chambre);
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

        return $this->render('_admin/chambre/edit.html.twig', [
            'chambre' => $chambre,
            'form' => $form->createView(),
            'titre' => 'Chambre',
        ]);
    }

    /**
     * @Route("/chambre/{id}/show", name="chambre_show", methods={"GET"})
     * @param chambre $chambre
     * @return Response
     */
    public function show(chambre $chambre): Response
    {
        $form = $this->createForm(ChambreType::class,$chambre, [
            'method' => 'POST',
            'action' => $this->generateUrl('chambre_show',[
                'id'=>$chambre->getId(),
            ])
        ]);

        return $this->render('_admin/chambre/voir.html.twig', [
            'chambre' => $chambre,
            'titre' => 'Chambre',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/chambre/{id}/active", name="chambre_active", methods={"GET"})
     * @param $id
     * @param Chambre $chambre
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function active($id,Chambre $chambre, EntityManagerInterface $entityManager): Response
    {

        if ($chambre->getActive() == 1){

            $chambre->setActive(0);

        }else{

            $chambre->setActive(1);

        }
        $entityManager->persist($chambre);
        $entityManager->flush();
        return $this->json([
            'code'=>200,
            'message'=>'ça marche bien',
            'active'=>$chambre->getActive(),
        ],200);


    }


    /**
     * @Route("/chambre/{id}/delete", name="chambre_delete", methods={"POST","GET","DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Chambre $chambre
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em,Chambre $chambre): Response
    {


        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'chambre_delete'
                    ,   [
                        'id' => $chambre->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->remove($chambre);
            $em->flush();

            $redirect = $this->generateUrl('chambre');

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
        return $this->render('_admin/chambre/delete.html.twig', [
            'chambre' => $chambre,
            'form' => $form->createView(),
        ]);
    }

}
