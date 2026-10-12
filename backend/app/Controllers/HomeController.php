<?php
require_once __DIR__ . '/../Repositories/HomeRepository.php';
require_once __DIR__ . '/../Helpers/Response.php';

class HomeController
{
    private HomeRepository $repo;

    public function __construct()
    {
        $this->repo = new HomeRepository();
    }

    public function index(): array
    {
        return Response::success("Home data", [
            "restaurants" => $this->repo->getActiveRestaurants(),
            "products"    => $this->repo->getLatestProducts()
        ]);
    }
}

