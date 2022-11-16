<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use App\Repository\ChambreRepository;
use App\Repository\ElementSalleRepository;
use App\Repository\RestoRepository;
use App\Repository\SalleRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="app_default", methods={"post", "get"})
     * @param ChambreRepository $repository
     * @param ElementSalleRepository $elementSalleRepository
     * @param RestoRepository $restoRepository
     * @param SalleRepository $salleRepository
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function indexFlorence(ChambreRepository $repository,ElementSalleRepository $elementSalleRepository,RestoRepository $restoRepository,SalleRepository $salleRepository,ServiceRepository $serviceRepository): Response
    {

        $data = $repository->findAll();
        $services = $serviceRepository->findAll();
        $restos = $restoRepository->findAll();
        $salles = $salleRepository->findAll();
        $element = $elementSalleRepository->findAll();
        return $this->render('_includes/index.html.twig',[
            'data'=>$data,
            'services'=>$services,
            'restos'=>$restos,
            'salles'=>$salles
        ]);
    }

    /**
     * @Route("admin/agenda",name="agenda")
     * @param CalendarRepository $repository
     * @return Response
     */
    public function calendar(CalendarRepository $repository,NormalizerInterface $normalizer)
    {
        $listes = $repository->getEvenement();
      $ligne = $repository->findAll();
      $rdvs = [];

      foreach ($ligne as $data){
          $rdvs [] = [
              'id'=>$data->getId(),
              'start'=>$data->getStart()->format('Y-m-d H:i:s'),
              'end'=>$data->getEnd()->format('Y-m-d H:i:s'),
              'description'=>$data->getDescription(),
              'title'=>$data->getTitle(),
              'allDay'=>$data->getAllDay(),
              'backgroundColor'=>$data->getBackgroundColor(),
              'borderColor'=>$data->getBorderColor(),
              'textColor'=>$data->getTextColor(),
          ];
      }

      $data =  json_encode($rdvs);
      //dd($data);

        return $this->render("calendar/calendar.html.twig",compact('data','listes'));
    }

    /**
     * @Route("/admin/dashboard", name="dashboard", methods={"GET", "POST"})
     * @return Response
     */
    public function dashboard(CalendarRepository $repository)
    {

        $listes = $repository->getEvenement();
        $lignes = $repository->findAll();
        $rdvs = [];

        foreach ($lignes as $data){
            $rdvs [] = [
                'id'=>$data->getId(),
                'start'=>$data->getStart()->format('Y-m-d H:i:s'),
                'end'=>$data->getEnd()->format('Y-m-d H:i:s'),
                'description'=>$data->getDescription(),
                'title'=>$data->getTitle(),
                'allDay'=>$data->getAllDay(),
                'backgroundColor'=>$data->getBackgroundColor(),
                'borderColor'=>$data->getBorderColor(),
                'textColor'=>$data->getTextColor(),
            ];
        }

        $data =  json_encode($rdvs);
        return $this->render('_admin/dashboard/index.html.twig',compact('data','listes'));
    }
    /**
     * @Route("/admin/{id}/event", name="event_detaiils", methods={"GET", "POST"})
     * @return Response
     */
    public function detailsEvent($id,CalendarRepository $repository)
    {
        return $this->render('_admin/dashboard/info.html.twig',[
            'titre'=>'EVENEMENT',
            'data'=>$repository->findOneBy(['id'=>$id])
        ]);
    }

    /**
     * @Route("/home", name="home", methods={"GET", "POST"})
     * @return Response
     */
    public function home()
    {
        return $this->render('enig/index.html.twig');
    }

}
