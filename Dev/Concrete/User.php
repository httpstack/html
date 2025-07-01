<?php
namespace Dev\Concrete;

use Dev\Abstracts\Model\BaseModel;
use Dev\Contracts\Services\CrudProviderInterface;
use Dev\Contracts\Model\AttributesInterface;
class User extends BaseModel
{
    // You might add specific methods for User if needed
    public function getFullName(): string
    {
        return $this->get('first_name') . ' ' . $this->get('last_name');
    }
}