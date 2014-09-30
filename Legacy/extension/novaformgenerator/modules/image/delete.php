<?php
$Module = $Params['Module'];
$object = eZContentObject::fetch($Params['objectID']);
$object->remove();
$object->purge();
return true;
