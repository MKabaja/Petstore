<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PetData;
use App\Enums\PetStatus;
use App\Exceptions\PetNotFoundException;
use App\Exceptions\PetStoreApiException;
use App\Exceptions\PetStoreUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @phpstan-type PetInput array{
 * id?:int,
 * name:string,
 * status:'available'|'pending'|'sold',
 * category_name:string|null,
 * tags:string[]|null,
 * photo_urls:string[]|null,
 * }
 * @phpstan-type PetApiResponse array{
 *   id: int,
 *   name: string,
 *   status: string,
 *   category?: array{id: int, name: string},
 *   tags?: array<array{id: int, name: string}>,
 *   photoUrls?: string[]
 * }
 * @phpstan-type PetPayload array{
 *   id?: int,
 *   name: string,
 *   status: string,
 *   category: array{id: int, name: string},
 *   tags: array<array{id: int, name: string}>,
 *   photoUrls: string[]
 * }
 */
final class PetService
{
    /**
     * @return PetData[]
     */
    public function findByStatus(string $status): array
    {
        try {
            $response = $this->petStoreClient()->get('/pet/findByStatus', [
                'status' => $status,
            ]);

            $this->resolveError($response);

            return array_map(
                fn (array $petData) => $this->fromApiResponse($petData),
                $response->json(),
            );
        } catch (ConnectionException) {
            throw new PetStoreUnavailableException;
        }
    }

    public function findById(int $id): PetData
    {
        try {
            $response = $this->petStoreClient()->get("/pet/{$id}");

            $this->resolveError($response);

            return $this->fromApiResponse($response->json());
        } catch (ConnectionException) {
            throw new PetStoreUnavailableException;
        }
    }

    /**
     * @param  PetInput  $data
     */
    public function create(array $data): PetData
    {
        try {
            $response = $this->petStoreClient()->post('/pet', $this->createPetPayload($data));

            $this->resolveError($response);

            return $this->fromApiResponse($response->json());
        } catch (ConnectionException) {
            throw new PetStoreUnavailableException;
        }
    }

    /**
     * @param  PetInput  $data
     */
    public function update(array $data): PetData
    {
        try {
            $response = $this->petStoreClient()->put('/pet', $this->createPetPayload($data));

            $this->resolveError($response);

            return $this->fromApiResponse($response->json());
        } catch (ConnectionException) {
            throw new PetStoreUnavailableException;
        }
    }

    public function destroy(int $id): void
    {
        try {
            $response = $this->petStoreClient()->delete("/pet/{$id}");
            $this->resolveError($response);
        } catch (ConnectionException) {
            throw new PetStoreUnavailableException;
        }
    }

    private function petStoreClient(): PendingRequest
    {
        return Http::withHeaders([
            'api_key' => config('petstore.api_key'),
        ])
            ->baseUrl(config('petstore.base_url'))
            ->timeout(config('petstore.timeout'))
            ->retry(
                config('petstore.retry'),
                100,
                fn ($exception) => $exception instanceof ConnectionException,
                false
            );

    }

    /**
     * @param  PetApiResponse  $dataFromAPI
     */
    private function fromApiResponse(array $dataFromAPI): PetData
    {
        return new PetData(
            id: $dataFromAPI['id'],
            name: $dataFromAPI['name'],
            status: PetStatus::tryFrom($dataFromAPI['status']) ?? PetStatus::UNKNOWN,
            categoryName: $dataFromAPI['category']['name'] ?? null,
            tags: array_column($dataFromAPI['tags'] ?? [], 'name'),
            photoUrls: $dataFromAPI['photoUrls'] ?? [],
        );
    }

    private function resolveError(Response $response): void
    {
        match (true) {
            $response->notFound() => throw new PetNotFoundException,
            $response->serverError() => throw new PetStoreUnavailableException,
            $response->clientError() => throw new PetStoreApiException,
            default => null,
        };
    }

    /**
     * @param  PetInput  $data
     * @return PetPayload
     */
    private function createPetPayload(array $data): array
    {
        $payload = [
            'name' => $data['name'],
            'status' => $data['status'],
            'category' => ['id' => 0, 'name' => $data['category_name'] ?? ''],
            'tags' => array_map(fn (string $tag) => ['id' => 0, 'name' => $tag], $data['tags'] ?? []),
            'photoUrls' => $data['photo_urls'] ?? [],
        ];

        if (isset($data['id'])) {
            $payload['id'] = $data['id'];
        }

        return $payload;
    }
}
