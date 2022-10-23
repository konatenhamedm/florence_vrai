<?php

namespace App\Controller;

use App\Repository\ChambreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{

    Private $base_url = "https://secure.sycapay.net/login";  // "https://dev.sycapay.com/";
    Private $marchandid = "C_624EC4E177601";
    Private $token;
    Private $telephone = "0505000001"; // "0554765502";
    Private $name = "SAHI";
    Private $pname = "Zoh Mondésir";
    Private $urlnotif;
    Private $montant = 2000;
    Private $currency = "XOF";
    Private $numcommande;
    Private $otp = "7908";

    /**
     * @Route("/api", name="app_api")
     */
    public function index(Request $request): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/error", name="app_api_error")
     */
    public function oups(Request $request): Response
    {
      // dd($request->getContent());
        return $this->render('api/error.html.twig', [
            'msg' => $request->get('msg') ?? 'Erreur inconnue',
        ]);
    }

    /**
     * @Route("/api/success", name="app_api_success")
     */
    public function success(Request $request): Response
    {// dd($request->getContent());
        return $this->render('api/success.html.twig', [
            'msg' => 'Success',
        ]);
    }

    /**
     * @Route("/api/commande", name="app_api_test", methods={"post"})
     */
    public function authentication(Request $request, ChambreRepository $repository): Response
    {
        $id = $request->get('id');
        $chambre = $repository->find($id);
        if($chambre){
            $amount = $chambre->getPrix() * $request->get('nbre'); $marchandid = $this->marchandid;
            $headers = array("X-SYCA-MERCHANDID: $marchandid",
                "X-SYCA-APIKEY: pk_syca_73f493ae42a761995a7f84b43f863002df7cbbf7",
                'X-SYCA-REQUEST-DATA-FORMAT: JSON',
                'X-SYCA-RESPONSE-DATA-FORMAT: JSON'
            );
            $paramsend = array(
                "montant" => $amount,
                "currency" => $this->currency
            );
            $url = $this->base_url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSLVERSION ,CURL_SSLVERSION_TLSv1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $data_json = json_encode($paramsend);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            $response = json_decode(curl_exec($ch),TRUE);
            if( empty($response) ){
                echo "Error Number:".curl_errno($ch)."<br>";
                echo "Error String:".curl_error($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }
            curl_close($ch);

            if($response && isset($response['code']) && $response['code'] == 0)
            {
                $time = date_format(date_create(), 'dmY');
                $token = $response['token'];
                $commande = "CMD00$id$time";
                $data = [
                    'token' => $token,
                    'amount' => $amount,
                    'currency' => $this->currency,
                    'urls' => 'http://localhost:8000/api/success',
                    'urlc' => 'http://localhost:8000/api/error',
                    'commande' => $commande,
                    'merchandid' => $this->marchandid,
                    'typepaie' => 'payement',
                ];
    
                return $this->render('api/paiement.html.twig',[
                    'data'=>$data
                ]);
            }
            else
            {
                return $this->redirectToRoute('app_api_error', [
                    'msg' => "Erreur lors de la connexion à l'API de paiement. Veuillez reessayer svp !"
                ]);
                // dd($response);
                echo $response['code'];
                echo $response['token'];
            }
            return $response;
        }

    }

     /**
     * @Route("/api/payment", name="app_payment_test")
     */
    public function payment(): Response
    {
        $json ="{
            \"marchandid\":\"".$this->marchandid."\",
            \"token\": \"".$token."\",
            \"telephone\": \"0000000000\",
            \"name\": \"".$this->name."\",
            \"pname\": \"".$this->pname."\",
            \"urlnotif\": \"".$this->urlnotif."\",
            \"montant\": \"".$this->montant."\",
            \"currency\": \"".$this->currency."\",
            \"numcommande\": \"".$this->numcommande."\"
            }";
            $url = $this->base_url."checkoutpay.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSLVERSION ,CURL_SSLVERSION_TLSv1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json" ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            $response = json_decode(curl_exec($ch));
            $err = curl_error($ch);
            curl_close($ch);
            var_dump($response);
        return true;
    }

     /**
     * @Route("/api/new", name="app_payment_new")
     */
    public function new(): Response
    {
        $headers = array('X-SYCA-MERCHANDID: C_624EC4E177601',
            'X-SYCA-APIKEY: pk_syca_73f493ae42a761995a7f84b43f863002df7cbbf7',
            'X-SYCA-REQUEST-DATA-FORMAT: JSON',
            'X-SYCA-RESPONSE-DATA-FORMAT: JSON'
        );
        $paramsend = array(
            "montant" => $this->montant,
            "currency" => $this->currency
        );
        $url = $this->base_url."login.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION ,CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data_json = json_encode($paramsend);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        $response = json_decode(curl_exec($ch),TRUE);
        if( empty($response) ){
            echo "Error Number:".curl_errno($ch)."<br>";
            echo "Error String:".curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        curl_close($ch);
        




        $headers = array ("X-SYCA-MERCHANDID: C94859958859", "X-SYCA-APIKEY:
        PK785774887383", 'X-SYCA-REQUEST-DATA-FORMAT: JSON','X-SYCA-RESPONSE-DATAFORMAT: JSON');
        $paramsend = array ("montant" =>"100", "curr" =>"XOF");
        $url = "https://secure.sycapay.net/login";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION ,CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data_json = json_encode($paramsend);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        $response = json_decode(curl_exec($ch),TRUE);
        If (empty ($response) ){echo "Error Number:".curl_errno($ch)."<br>"; echo "Error 
        String:".curl_error($ch); $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);}
        curl_close($ch); if($response['code'] == 0)
        {$token = $response['token'];}
        else {echo $response['code']; echo $response['token'];}

        return $response;
    }
}
