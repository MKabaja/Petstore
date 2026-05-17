<?php

declare(strict_types=1);

use App\Exceptions\PetNotFoundException;
use App\Exceptions\PetStoreUnavailableException;
use App\Services\PetService;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {

    $this->service = $this->mock(PetService::class);
});

describe('index', function () {

    it('returns 200 with pet listing', function () {

        $this->service->shouldReceive('findByStatus')
            ->once()
            ->with('available')
            ->andReturn([expectedPetData()]);

        $this->get(route('pets.index'))
            ->assertOk()
            ->assertViewIs('pets.index')
            ->assertViewHas('pets');
    });
    it('filters by status', function () {

        $this->service->shouldReceive('findByStatus')
            ->once()
            ->with('pending')
            ->andReturn([expectedPetData()]);

        $this->get(route('pets.index', ['status' => 'pending']))
            ->assertOk()
            ->assertViewIs('pets.index')
            ->assertViewHas('pets');
    });
    it('shows error flash when service unavailable', function () {

        $this->service->shouldReceive('findByStatus')
            ->once()
            ->andThrow(PetStoreUnavailableException::class);

        $this->get(route('pets.index'))
            ->assertRedirect()
            ->assertSessionHas('error', 'Service unavailable. Please try again later.');
    });
});
describe('show', function () {
    it('returns 200 with pet details', function () {
        $this->service->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn(expectedPetData());

        $this->get(route('pets.show', ['id' => 1]))
            ->assertOk()
            ->assertViewIs('pets.show')
            ->assertViewHas('pet');
    });
    it('redirects to index when pet not found', function () {
        $this->service->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andThrow(PetNotFoundException::class);

        $this->get(route('pets.show', ['id' => 1]))
            ->assertRedirect(route('pets.index'))
            ->assertSessionHas('error', 'Pet not found.');
    });
});

describe('create', function () {
    it('returns 200 with create form', function () {
        $this->get(route('pets.create'))
            ->assertOk()
            ->assertViewIs('pets.create');
    });
});

describe('store', function () {
    it('creates pet and redirects to index with success flash', function () {
        $this->service->shouldReceive('create')
            ->once()
            ->with(petInput())
            ->andReturn(expectedPetData());

        $this->post(route('pets.store'), petInput())
            ->assertRedirect(route('pets.index'))
            ->assertSessionHas('success', 'Pet created successfully.');
    });

    it('returns validation errors for invalid data', function () {
        $this->post(route('pets.store'), [])
            ->assertSessionHasErrors(['name', 'status']);
    });
});

describe('edit', function () {
    it('returns 200 with edit form', function () {
        $this->service->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn(expectedPetData());

        $this->get(route('pets.edit', ['id' => 1]))
            ->assertOk()
            ->assertViewIs('pets.edit')
            ->assertViewHas('pet');
    });

    it('redirects to index when pet not found', function () {
        $this->service->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andThrow(PetNotFoundException::class);

        $this->get(route('pets.edit', ['id' => 1]))
            ->assertRedirect(route('pets.index'))
            ->assertSessionHas('error', 'Pet not found.');
    });
});

describe('update', function () {
    it('updates pet and redirects to show with success flash', function () {

        $this->service->shouldReceive('update')
            ->once()
            ->with([
                'id' => 1,
                ...petInput(),
            ])
            ->andReturn(expectedPetData());

        $this->put(route('pets.update', ['id' => 1]), petInput())
            ->assertRedirect(route('pets.show', ['id' => 1]))
            ->assertSessionHas('success', 'Pet updated successfully.');
    });

    it('redirects to index when pet not found', function () {
        $this->service->shouldReceive('update')
            ->once()
            ->with([
                'id' => 1,
                ...petInput(),
            ])->andThrow(PetNotFoundException::class);

        $this->put(route('pets.update', ['id' => 1]), petInput())
            ->assertRedirect(route('pets.index'))
            ->assertSessionHas('error', 'Pet not found.');
    });

    it('returns validation errors for invalid data', function () {
        $this->put(route('pets.update', ['id' => 1]), [])
            ->assertSessionHasErrors(['name', 'status']);
    });

});

describe('destroy', function () {
    it('deletes pet and redirects to index with success flash', function () {
        $this->service->shouldReceive('destroy')
            ->once()
            ->with(1);

        $this->delete(route('pets.destroy', ['id' => 1]))
            ->assertRedirect(route('pets.index'))
            ->assertSessionHas('success', 'Pet deleted successfully.');
    });
    it('redirects to index when pet not found', function () {
        $this->service->shouldReceive('destroy')
            ->once()
            ->with(1)
            ->andThrow(PetNotFoundException::class);

        $this->delete(route('pets.destroy', ['id' => 1]))
            ->assertRedirect(route('pets.index'))
            ->assertSessionHas('error', 'Pet not found.');
    });
});
