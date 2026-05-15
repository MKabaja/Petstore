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
use Illuminate\Support\Facades\Cache;
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
 *   name?: string|null,
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
    private const CACHE_PREFIX = 'pets.';

    /**
     * @return PetData[]
     */
    public function findByStatus(string $status): array
    {
        return Cache::remember(
            self::CACHE_PREFIX.$status,
            config('petstore.cache_ttl'),
            fn () => $this->execute(function () use ($status) {

                $response = $this->petStoreClient()->get('/pet/findByStatus', [
                    'status' => $status,
                ]);

                $this->ensureSuccessfulResponse($response);

                return array_map(
                    fn (array $petData) => $this->fromApiResponse($petData),
                    $response->json(),
                );
            })
        );

    }

    public function findById(int $id): PetData
    {
        return $this->execute(function () use ($id) {
            $response = $this->petStoreClient()->get("/pet/{$id}");
            $this->ensureSuccessfulResponse($response);

            return $this->fromApiResponse($response->json());
        });

    }

    /**
     * @param  PetInput  $data
     */
    public function create(array $data): PetData
    {
        return $this->execute(function () use ($data) {
            $response = $this->petStoreClient()->post('/pet', $this->assemblePetPayload($data));

            $this->ensureSuccessfulResponse($response);
            $this->invalidateCache();

            return $this->fromApiResponse($response->json());
        });

    }

    /**
     * @param  PetInput  $data
     */
    public function update(array $data): PetData
    {
        return $this->execute(function () use ($data) {
            $response = $this->petStoreClient()->put('/pet', $this->assemblePetPayload($data));

            $this->ensureSuccessfulResponse($response);
            $this->invalidateCache();

            return $this->fromApiResponse($response->json());
        });

    }

    public function destroy(int $id): void
    {
        $this->execute(function () use ($id) {
            $response = $this->petStoreClient()->delete("/pet/{$id}");
            $this->ensureSuccessfulResponse($response);
            $this->invalidateCache();
        });

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
     * @param  PetApiResponse  $data
     */
    private function fromApiResponse(array $data): PetData
    {
        return new PetData(
            id: $data['id'],
            name: $data['name'] ?? '',
            status: PetStatus::tryFrom($data['status']) ?? PetStatus::UNKNOWN,
            categoryName: $data['category']['name'] ?? null,
            tags: array_column($data['tags'] ?? [], 'name'),
            photoUrls: $data['photoUrls'] ?? [],
        );
    }

    private function ensureSuccessfulResponse(Response $response): void
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
    private function assemblePetPayload(array $data): array
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

    /**
     * @template T
     *
     * @param  callable(): T  $action
     * @return T
     */
    private function execute(callable $action): mixed
    {
        try {
            return $action();

        } catch (ConnectionException) {
            throw new PetStoreUnavailableException;
        }

    }

    private function invalidateCache(): void
    {
        foreach (PetStatus::cases() as $status) {
            Cache::forget(self::CACHE_PREFIX.$status->value);
        }
    }
}
