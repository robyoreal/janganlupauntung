<?php
namespace App\Livewire\SalesForce;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sales;

class Index extends Component
{
    use WithPagination;
    public $showForm = false, $editingId = null, $name, $phone, $email;
    protected $rules = ['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:255', 'email' => 'nullable|email|max:255'];
    
    public function create() { $this->reset(['name', 'phone', 'email', 'editingId']); $this->showForm = true; }
    public function edit($id) { $sales = Sales::findOrFail($id); $this->editingId = $id; $this->name = $sales->name; $this->phone = $sales->phone; $this->email = $sales->email; $this->showForm = true; }
    public function save() { $this->validate(); $data = ['name' => $this->name, 'phone' => $this->phone, 'email' => $this->email]; if ($this->editingId) { Sales::find($this->editingId)->update($data); session()->flash('message', 'Sales person updated successfully.'); } else { Sales::create($data); session()->flash('message', 'Sales person created successfully.'); } $this->cancel(); }
    public function delete($id) { Sales::destroy($id); session()->flash('message', 'Sales person deleted successfully.'); }
    public function cancel() { $this->showForm = false; $this->reset(['name', 'phone', 'email', 'editingId']); }
    public function render() { return view('livewire.sales-force.index', ['salesforce' => Sales::orderBy('name')->paginate(15)])->layout('layouts.app'); }
}
