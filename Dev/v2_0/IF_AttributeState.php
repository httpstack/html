<?php 
namespace Dev\v2_0;
interface IF_AttributeState {
    public function saveState(string $restorePoint): bool;
    public function restoreState(string $restorePoint): bool;
}
.//;;?>++
+