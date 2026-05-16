<?php

declare(strict_types=1);
use App\DTOs\PetData;
use App\Enums\PetStatus;
use App\Exceptions\PetNotFoundException;
use App\Exceptions\PetStoreApiException;
use App\Exceptions\PetStoreUnavailableException;
use App\Services\PetService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use Tests\TestCase;

uses(TestCase::class);

function petApiResponse(): array
{
    return [
        'id' => 1,
        'name' => 'Lilo',
        'status' => 'available',
        'category' => ['id' => 1, 'name' => 'Dogs'],
        'tags' => [['id' => 1, 'name' => 'friendly']],
        'photoUrls' => ['https://example.com/dog.jpg'],
    ];
}

function expectedPetData(): PetData
{
    return new PetData(
        id: 1,
        name: 'Lilo',
        status: PetStatus::AVAILABLE,
        categoryName: 'Dogs',
        tags: ['friendly'],
        photoUrls: ['https://example.com/dog.jpg'],
    );
}

function petInput(): array
{
    return [
        'name' => 'Lilo',
        'status' => 'available',
        'category_name' => 'Dogs',
        'tags' => ['friendly'],
        'photo_urls' => ['https://example.com/dog.jpg'],
    ];
}

function petService(): PetService
{
    return new PetService;
}

beforeEach(function () {
    Cache::flush();
});

describe('findByStatus', function () {

    it('returns array of PetData for valid status', function () {
        Http::fake([
            '*/pet/findByStatus*' => Http::response([petApiResponse()], 200),
        ]);
        $result = petService()->findByStatus('available');
        expect($result)
            ->toBeArray()
            ->toHaveCount(1)
            ->and($result[0])->toEqual(expectedPetData());
    });

    it('returns cached results on second call', function () {
        Http::fake([
            '*/pet/findByStatus*' => Http::response([petApiResponse()], 200),
        ]);
        $service = petService();
        $service->findByStatus('available');
        $service->findByStatus('available');
        Http::assertSentCount(1);
    });

    it('throws PetStoreUnavailableException on connection error', function () {
        Http::fake([
            '*' => fn () => throw new ConnectionException,
        ]);
        expect(fn () => petService()->findByStatus('available'))
            ->toThrow(PetStoreUnavailableException::class);
    });

    it('throws PetStoreApiException on HTTP error', function () {
        Http::fake([
            '*/pet/findByStatus*' => Http::response([], 400),
        ]);
        expect(fn () => petService()->findByStatus('available'))
            ->toThrow(PetStoreApiException::class);
    });
});

describe('findById', function () {
    it('returns PetData for valid id', function () {
        Http::fake([
            '*/pet/*' => Http::response(petApiResponse(), 200),
        ]);
        $result = petService()->findById(1);
        expect($result)
            ->toBeInstanceOf(PetData::class)
            ->and($result)->toEqual(expectedPetData());
    });

    it('throws PetNotFoundException when pet does not exist', function () {
        Http::fake([
            '*/pet/*' => Http::response([], 404),
        ]);

        expect(fn () => petService()->findById(99999))
            ->toThrow(PetNotFoundException::class);
    });

    it('throws PetStoreUnavailableException on connection error', function () {
        Http::fake([
            '*' => fn () => throw new ConnectionException,
        ]);
        expect(fn () => petService()->findById(1))
            ->toThrow(PetStoreUnavailableException::class);
    });
});

describe('create', function () {
    it('returns PetData after successful creation', function () {
        Http::fake([
            '*/pet' => Http::response(petApiResponse(), 200),
        ]);
        $result = petService()->create(petInput());
        expect($result)
            ->toBeInstanceOf(PetData::class)
            ->and($result)->toEqual(expectedPetData());
    });

    it('invalidates cache after creation', function () {
        Http::fake([
            '*/pet/findByStatus*' => Http::response([petApiResponse()], 200),
            '*/pet*' => Http::response(petApiResponse(), 200),
        ]);

        petService()->findByStatus('available');
        expect(Cache::has('pets.available'))->toBeTrue();

        petService()->create(petInput());
        expect(Cache::has('pets.available'))->toBeFalse();
    });

    it('throws PetStoreUnavailableException on connection error', function () {
        Http::fake([
            '*' => fn () => throw new ConnectionException,
        ]);
        expect(fn () => petService()->create(petInput()))
            ->toThrow(PetStoreUnavailableException::class);
    });
});

describe('update', function () {
    it('returns PetData after successful update', function () {
        Http::fake([
            '*/pet' => Http::response(petApiResponse(), 200),
        ]);
        $result = petService()->update([...petInput(), 'id' => 1]);
        expect($result)
            ->toBeInstanceOf(PetData::class)
            ->and($result)->toEqual(expectedPetData());
    });

    it('invalidates cache after update', function () {
        Http::fake([
            '*/pet/findByStatus*' => Http::response([petApiResponse()], 200),
            '*/pet*' => Http::response(petApiResponse(), 200),
        ]);

        petService()->findByStatus('available');
        expect(Cache::has('pets.available'))->toBeTrue();

        petService()->update([...petInput(), 'id' => 1]);
        expect(Cache::has('pets.available'))->toBeFalse();
    });

    it('throws PetNotFoundException when pet does not exist', function () {
        Http::fake([
            '*/pet' => Http::response([], 404),
        ]);

        expect(fn () => petService()->update([...petInput(), 'id' => 99999]))
            ->toThrow(PetNotFoundException::class);
    });

    it('throws PetStoreUnavailableException on connection error', function () {
        Http::fake([
            '*' => fn () => throw new ConnectionException,
        ]);
        expect(fn () => petService()->update([...petInput(), 'id' => 1]))
            ->toThrow(PetStoreUnavailableException::class);
    });
});

describe('destroy', function () {
    it('returns void after successful deletion', function () {
        Http::fake([
            '*/pet/*' => Http::response(null, 200),
        ]);

        petService()->destroy(1);

    })->throwsNoExceptions();

    it('invalidates cache after deletion', function () {
        Http::fake([
            '*/pet/findByStatus*' => Http::response([petApiResponse()], 200),
            '*/pet/*' => Http::response(petApiResponse(), 200),
        ]);

        petService()->findByStatus('available');
        expect(Cache::has('pets.available'))->toBeTrue();

        petService()->destroy(1);
        expect(Cache::has('pets.available'))->toBeFalse();
    });

    it('throws PetNotFoundException when pet does not exist', function () {
        Http::fake([
            '*/pet/*' => Http::response([], 404),
        ]);

        expect(fn () => petService()->destroy(99999))
            ->toThrow(PetNotFoundException::class);
    });

    it('throws PetStoreUnavailableException on connection error', function () {
        Http::fake([
            '*' => fn () => throw new ConnectionException,
        ]);
        expect(fn () => petService()->destroy(1))
            ->toThrow(PetStoreUnavailableException::class);
    });
});
