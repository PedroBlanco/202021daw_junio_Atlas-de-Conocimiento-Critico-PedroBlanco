<?php

namespace App\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Livewire\Component;
use App\Models\Administracion;

class WireAdministracion extends Component
{
    use AuthorizesRequests;

    public $contenedor;
    public $nombre, $descripcion, $_id;
    public $isOpen = 0;
    public $model = App\Models\Administracion::class;

    public $mensajes = array(
        'titulo_pagina' => 'Gestión de Administraciones',
        'boton_crear' => 'Crear nueva Administración'
    );

    public function render()
    {
        $this->authorize('viewAny', Administracion::class);

        $this->contenedor = Administracion::latest()->get();

        return view('livewire.generic.list');
    }

    public function create()
    {
        $this->authorize('create', Administracion::class);

        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields(){
        $this->nombre = '';
        $this->descripcion = '';
        $this->_id = '';
    }

    public function store()
    {
        if ( null !== $this->_id ) {
            $this->authorize('update', Administracion::findOrFail($this->_id));
        } else {
            $this->authorize('create', Administracion::class);
        }

        $this->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
        ]);

        Administracion::updateOrCreate(['id' => $this->_id], [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion
        ]);

        session()->flash('message',
            $this->_id ? 'Administración definida correctamente.' : 'Administración actualizada correctamente.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $administracion = Administracion::findOrFail($id);

        $this->authorize('update', $administracion);

        $this->_id = $id;
        $this->nombre = $administracion->nombre;
        $this->descripcion = $administracion->descripcion;

        $this->openModal();
    }

    public function delete($id)
    {
        $administracion = Administracion::find($id);

        $this->authorize('delete', $administracion);

        $administracion->delete();
        session()->flash('message', 'Administración borrada correctamente.');
    }
}

