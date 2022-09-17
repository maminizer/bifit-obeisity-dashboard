<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ObesityPredictionController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
    
    /**
     * @Route("/predict", name="app_obesity_prediction")
     */ 
    public function predict(Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('child', NumberType::class)
            ->add('farm', NumberType::class)
            ->add('recfa', NumberType::class)
            ->add('restaurent', NumberType::class)
            ->add('store', NumberType::class)
            //->add('Predict', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            $response = $this->client->request(
                'GET',
                'http://127.0.0.1:5000/predict?child=' . $data["child"] . '&farm=' . $data["farm"] . '&restaurent=' . $data["restaurent"] . '&recfa=' . $data["recfa"] .'&store='. $data["store"] . ''
            );

            return $this->render('obesity_prediction/index.html.twig', [
                'controller_name' => 'ObesityPredictionController',
                'obesity_rate'        =>   $response->toArray()["Predicted_obesity"],
                'Average_obesity'        =>   $response->toArray()["Average_obesity"],
                'Obesity_evaluation'        =>   $response->toArray()["Obesity_evaluation"],
                'form'            => $form->createView(),
            ]);
        }

        return $this->render('obesity_prediction/index.html.twig', [
            'controller_name' => 'ObesityPredictionController',
            'form'            => $form->createView(),
            'obesity_rate'        =>   "",
            'Average_obesity'        =>   "",
            'Obesity_evaluation'        =>   "",
        ]);
    }
    /**
     * @Route("/desc", name="app_desc")
     */ 
    public function desc(Request $request): Response
    {
        return $this->render('obesity_prediction/desc.html.twig');
    }
    /**
     * @Route("/clust", name="app_clust")
     */ 
    public function clust(Request $request): Response
    {
        return $this->render('obesity_prediction/Clustering.html.twig');
    }
}


