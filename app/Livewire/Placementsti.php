<?php

namespace App\Livewire;

use App\Models\Placement2;
use Livewire\Component;
use Livewire\WithPagination;

class Placementsti extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $nama_placement, $id, $is_update = false;

    public function cancel()
    {
        $this->is_update = true;
        $this->reset();
    }
    public function add()
    {
        $this->validate([
            'nama_placement' => 'required'
        ]);
        $data = new Placement2;
        $data->nama_placement = $this->nama_placement;
        $data->save();
        $this->reset();
        // $this->dispatch('success', message: 'Placement added');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Placement added',
        );
    }

    public function edit($id)
    {
        $this->is_update = true;
        $placement = Placement2::find($id);
        $this->id = $placement->id;
        $this->nama_placement = $placement->nama_placement;
    }

    public function update()
    {
        $data = Placement2::find($this->id);
        $data->nama_placement = $this->nama_placement;
        $data->save();
        $this->is_update = false;
        $this->reset();
        // $this->dispatch('success', message: 'Placement updated');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Placement updated',
        );
    }

    public function delete($id)
    {
        $data = Placement2::find($id);
        $data->delete();
        // $this->dispatch('success', message: 'Placement deleted');
        $this->dispatch(
            'message',
            type: 'success',
            title: 'Placement deleted',
        );
    }
    public function render()
    {
        $datas = Placement2::paginate(10);
        return view('livewire.placementsti', [
            'datas' => $datas
        ]);
    }
}
