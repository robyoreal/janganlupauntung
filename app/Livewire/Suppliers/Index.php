<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class Index extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingId = null;
    public $name, $contact, $phone, $address;

    protected $rules = [
        'name' => 'required|string|max:255',
        'contact' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string',
    ];

    public function create()
    {
        $this->reset(['name', 'contact', 'phone', 'address', 'editingId']);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->editingId = $id;
        $this->name = $supplier->name;
        $this->contact = $supplier->contact;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'contact' => $this->contact,
            'phone' => $this->phone,
            'address' => $this->address,
        ];

        if ($this->editingId) {
            Supplier::find($this->editingId)->update($data);
            session()->flash('message', 'Supplier updated successfully.');
        } else {
            Supplier::create($data);
            session()->flash('message', 'Supplier created successfully.');
        }

        $this->cancel();
    }

    public function delete($id)
    {
        Supplier::destroy($id);
        session()->flash('message', 'Supplier deleted successfully.');
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->reset(['name', 'contact', 'phone', 'address', 'editingId']);
    }

    public function render()
    {
        $suppliers = Supplier::orderBy('name')->paginate(15);
        return view('livewire.suppliers.index', ['suppliers' => $suppliers])->layout('layouts.app');
    }
}
