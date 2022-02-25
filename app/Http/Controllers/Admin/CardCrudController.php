<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CardRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CardCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CardCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Card::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/card');
        CRUD::setEntityNameStrings('card', 'cards');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Columnas Lista
        $this->addColumns();

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CardRequest::class);

        $this->addFields();

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     *  Fields for the list of cards
     */
    private function addFields()
    {
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Nombre'
            ],
            [
                'name' => 'description',
                'label' => 'Descripción',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 10,
                    'cols' => 10
                ]
            ],
            [
                'label'     => "Colecciones",
                'type'      => 'select_multiple',
                'name'      => 'collections',
                'entity'    => 'collections',
                'attribute' => 'name',
                'pivot'     => true,
            ]
        ]);
    }

    /**
     *  Fields to card list
     */
    private function addColumns()
    {
        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => 'Nombre'
            ],
            [
                'name' => 'description',
                'label' => 'Descripción'
            ],
            [
                'name' => 'collections',
                'label' => 'Colecciones Asociadas',
                'entity'    => 'collections',
                'model'     => "App\Models\Collection",
                'attribute' => 'name',
                'pivot'     => true,
            ],
            [
                'name' => 'created_at',
                'label' => 'Creado el:'
            ],
            [
                'name' => 'updated_at',
                'label' => 'Actualizado el:'
            ]
        ]);
    }
}