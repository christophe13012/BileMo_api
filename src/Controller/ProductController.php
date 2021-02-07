<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="products", methods={"GET"})
     */
    public function index(ProductsRepository $productsRepository): Response
    {
        $products = $productsRepository->findAll();
        return $this->json($products, 200);
    }

    /**
     * @Route("/api/product/{id}", name="product", methods={"GET"})
     */
    public function getPhone(ProductsRepository $productsRepository, int $id): Response
    {
        $products = $productsRepository->find($id);
        return $this->json($products, 200);
    }
}
