<?php
namespace App\Livewire\Customers;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

class Index extends Component
{
    use WithPagination;
    public $showForm = false, $editingId = null, $name, $phone, $address;
    protected $rules = ['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:255', 'address' => 'nullable|string'];
    
    public function create() { $this->reset(['name', 'phone', 'address', 'editingId']); $this->showForm = true; }
    public function edit($id) { $customer = Customer::findOrFail($id); $this->editingId = $id; $this->name = $customer->name; $this->phone = $customer->phone; $this->address = $customer->address; $this->showForm = true; }
    public function save() { $this->validate(); $data = ['name' => $this->name, 'phone' => $this->phone, 'address' => $this->address]; if ($this->editingId) { Customer::find($this->editingId)->update($data); session()->flash('message', 'Customer updated successfully.'); } else { Customer::create($data); session()->flash('message', 'Customer created successfully.'); } $this->cancel(); }
    public function delete($id) { Customer::destroy($id); session()->flash('message', 'Customer deleted successfully.'); }
    public function cancel() { $this->showForm = false; $this->reset(['name', 'phone', 'address', 'editingId']); }
    public function render() { return view('livewire.customers.index', ['customers' => Customer::orderBy('name')->paginate(15)])->layout('layouts.app'); }
}
