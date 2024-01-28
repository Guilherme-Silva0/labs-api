<?php

/**
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

declare(strict_types=1);

namespace App\Controller;

use App\Model\Product as ProductModel;
use Ramsey\Uuid\Uuid;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

final class Product extends AbstractController
{
    public function index(): Psr7ResponseInterface
    {
        $products = ProductModel::all();
        return $this->response->json($products);
    }

    public function create()
    {

        $user = $this->container->get('user');

        $name = $this->request->input('name');
        $description = $this->request->input('description');
        $squadId = $this->request->input('squad_id');

        $product = new ProductModel();

        $product->uuid = Uuid::uuid4()->toString();
        $product->owner_uuid = $user->uuid;
        $product->name = $name;
        $product->description = $description;
        $product->save();

        return $this->response->json([
            'message' => 'Produto cadastrado com sucesso!',
            'product' => $product,
        ]);
    }

    public function show($uuid): Psr7ResponseInterface
    {
        $product = ProductModel::where('uuid', $uuid)->first();

        if (! $product) {
            return $this->response->json(['error' => 'Product not found'], 404);
        }

        return $this->response->json($product);
    }

    public function update($uuid): Psr7ResponseInterface
    {
        $product = ProductModel::where('uuid', $uuid)->first();

        if (! $product) {
            return $this->response->json(['error' => 'Product not found'], 404);
        }

        $name = $this->request->input('name');
        $description = $this->request->input('description');

        $product->name = $name;
        $product->description = $description;
        $product->save();

        return $this->response->json([
            'message' => 'Product updated successfully!',
            'product' => $product,
        ]);
    }

    /**
     * Delete a product.
     * @RequestMapping(path="delete/{id}", methods="post")
     * @param mixed $id
     */
    public function delete($uuid): Psr7ResponseInterface
    {
        $product = ProductModel::where('uuid', $uuid)->first();

        if (! $product) {
            return $this->response->json(['error' => 'Product not found'], 404);
        }

        $product->delete();

        return $this->response->json(['message' => 'Product deleted successfully!']);
    }
}