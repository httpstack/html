<?php 
namespace Dev\v2_0;
interface AttributeStateInterface {
    public function saveState(string $restorePoint): bool;
    public function restoreState(string $restorePoint): bool;
}
?>