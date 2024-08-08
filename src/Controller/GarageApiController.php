<?php

namespace App\Controller;

use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Car;


class GarageApiController extends AbstractController
{
    //* Requête pour récupérer un véhicule par son immatriculation */
    #[Route('/api/cars/{registration}', name: 'get.car', methods:['GET'])]
    public function getACar(
        CarRepository $repository,
        string $registration): JsonResponse
    {
        $carSelected = $repository->findOneBy(['registration' => $registration]);
        $car = [];

        if($carSelected->getRegistration() === $registration) {
            $car = [
                'id' => $carSelected->getId(),
                'registration' => $carSelected->getRegistration(),
                'brand' => $carSelected->getBrand(),
                'model' => $carSelected->getModel(),
                'status' => $carSelected->getStatus(),
                'createdAt' => $carSelected->getCreatedAt()
            ];
        }else{
            return new JsonResponse ("Error Processing Request");
        }

        return new JsonResponse($car);
    }

    //* Requête pour modifier un véhicule par son immatriculation */
    #[Route('/api/cars/edit/{registration}', name: 'edit.car', methods:['PUT'])]
    public function editCar(
        CarRepository $repository,
        EntityManagerInterface $manager,
        Request $request,
        string $registration): JsonResponse 
    {
        $carSelected = json_decode($request->getContent(), true);
        $editCar = $repository->findOneBy(['registration' => $registration]);

        if($editCar->getRegistration() === $registration){
            if (isset($carSelected['registration'])) {
                $editCar->setRegistration(strtoupper(str_replace('-', '', $carSelected['registration'])));
            }
            if(isset($carSelected['brand'])){
                $editCar->setBrand($carSelected['brand']);
            }
            if(isset($carSelected['model'])){
                $editCar->setModel($carSelected['model']);
            }
            if (isset($carSelected['status'])) {
                $editCar->setStatus($carSelected['status']);
            }

            $manager->persist($editCar);
            $manager->flush();
        }else {
            return error_log("Erreur dans la modification");
        }
        
        
        return new JsonResponse(200);
    }

    //* Requête pour créer un véhicule */
    #[Route('/api/cars/new', name: 'new.car', methods:['POST'])]
    public function newCar(
        EntityManagerInterface $manager,
        Request $request
    ) : JsonResponse {
        
        $dataCar = json_decode($request->getContent(), true);
        $newCar = new Car();
        
        if (isset($dataCar['registration'])) {
            $newCar->setRegistration(strtoupper(str_replace('-', '', $dataCar['registration'])));
        }
        if(isset($dataCar['brand'])){
            $newCar->setBrand($dataCar['brand']);
        }
        if(isset($carSelected['model'])){
            $newCar->setModel($dataCar['model']);
        }
        if (isset($carSelected['status'])) {
            $newCar->setStatus($dataCar['status']);
        }

        $manager->persist($newCar);
        $manager->flush();

        return new JsonResponse(200);
    }

    //* Requête pour supprimer un véhicule par son immatriculation */
    #[Route('/api/cars/delete/{registration}', name: 'delete.car', methods:['DELETE'])]
    public function deleteCar(
        CarRepository $repository,
        EntityManagerInterface $manager,
        string $registration): JsonResponse 
    {
        $carSelected = $repository->findOneBy(['registration' => $registration]);

        if ($carSelected->getRegistration() === $registration) {
            $manager->remove($carSelected);
            $manager->flush();
        } else {
            return ('Erreur dans la suppression');
        }
        
        
        return new JsonResponse(200);
    }
}
