<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\PetNotFoundException;
use App\Http\Requests\IndexPetRequest;
use App\Http\Requests\PetRequest;
use App\Services\PetService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PetController extends Controller
{
    public function __construct(
        private readonly PetService $petService,
    ) {}

    public function index(IndexPetRequest $request): View
    {
        $validated = $request->validated();

        $status = $validated['status'] ?? 'available';
        $tag = $validated['tag'] ?? null;

        $page = $request->integer('page', 1);

        $pets = $this->petService->findByStatus($status);

        $filteredPets = $tag
            ? array_filter($pets, fn ($pet) => in_array($tag, $pet->tags))
            : $pets;

        $paginatedPets = collect($filteredPets)
            ->values()
            ->forPage($page, 20);

        return view('pets.index', [
            'pets' => $paginatedPets,
            'status' => $status,
            'tag' => $tag,
            'page' => $page,
            'total' => count($filteredPets),
        ]);
    }

    public function create(): View
    {
        return view('pets.create');
    }

    public function store(PetRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->petService->create($validated);

        return redirect()
            ->route('pets.index')
            ->with('success', 'Pet created successfully.');
    }

    public function show(string $id): View|RedirectResponse
    {
        try {
            $pet = $this->petService->findById((int) $id);

            return view('pets.show', ['pet' => $pet]);
        } catch (PetNotFoundException $e) {
            return redirect()
                ->route('pets.index')
                ->with('error', $e->getMessage());
        }
    }

    public function edit(string $id): View|RedirectResponse
    {
        try {
            $pet = $this->petService->findById((int) $id);

            return view('pets.edit', ['pet' => $pet]);
        } catch (PetNotFoundException $e) {
            return redirect()
                ->route('pets.index')
                ->with('error', $e->getMessage());
        }
    }

    public function update(PetRequest $request, string $id): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $payload = [...$validated, 'id' => (int) $id];
            $this->petService->update($payload);

            return redirect()
                ->route('pets.show', ['id' => $id])
                ->with('success', 'Pet updated successfully.');
        } catch (PetNotFoundException $e) {
            return redirect()
                ->route('pets.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->petService->destroy((int) $id);

            return redirect()->route('pets.index')
                ->with('success', 'Pet deleted successfully.');
        } catch (PetNotFoundException $e) {
            return redirect()->route('pets.index')
                ->with('error', $e->getMessage());
        }
    }
}
