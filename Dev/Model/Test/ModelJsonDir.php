<?php 
namespace Dev\Model;
use Dev\Model\Absracts\AbstractModel;
use Dev\Contracts\CrudInterface;

class ModelJsonDir extends AbstractModel implements CrudInterface{
    /**
     * The Model is just a set of properties with 
     * CRUD operations to manipulate the actual data that the model represents, in real time
     * The actiual data, the datasource, is any class that has the CRUD interface implemented
     * This class is required in the construct and passed to the parent.
     * To populate thge model and have the data readily available, the parent::__construct will 
     * call the read method on your supplied datasource.
     * @var array
     */

    public function __construct(CrudInterface $jsonDirDatasource) {
        parent::__construct($jsonDirDatasource);
    }

    public function create(string $endpoint, array $params = []): array {
        // Simulate a create operation
        //the implemtation of this method will depend on the actual datasource
        return ['status' => 'success', 'data' => $params];
    }

    public function read(): void {
        $this->setAll($this->datasource->read());
        // Simulate a read operation
        return ['status' => 'success', 'data' => $this->getAll()];
    }

    public function update(string $endpoint, array $data): array {
        // Simulate an update operation
        $this->set($data);
        return ['status' => 'success', 'data' => $this->getAll()];
    }

    public function delete(string $endpoint): array {
        // Simulate a delete operation
        $this->clear();
        return ['status' => 'success'];
    }
    
}
?>