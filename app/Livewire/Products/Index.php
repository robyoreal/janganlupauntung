<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $editingId = null;

    public $name, $sku, $description, $cost_price, $selling_price, $stock;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'nullable|string|max:255|unique:products,sku',
        'description' => 'nullable|string',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['name', 'sku', 'description', 'cost_price', 'selling_price', 'stock', 'editingId']);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->editingId = $id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->cost_price = $product->cost_price;
        $this->selling_price = $product->selling_price;
        $this->stock = $product->stock;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingId) {
            $rules['sku'] = 'nullable|string|max:255|unique:products,sku,' . $this->editingId;
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'stock' => $this->stock,
        ];

        if ($this->editingId) {
            Product::find($this->editingId)->update($data);
            session()->flash('message', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('message', 'Product created successfully.');
        }

        $this->cancel();
    }

    public function delete($id)
    {
        Product::destroy($id);
        session()->flash('message', 'Product deleted successfully.');
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->reset(['name', 'sku', 'description', 'cost_price', 'selling_price', 'stock', 'editingId']);
    }

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('sku', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.products.index', [
            'products' => $products,
        ])->layout('layouts.app');
    }
}
