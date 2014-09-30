<?php

$Module = array( 'name' => 'Event place management',
                 'variable_params' => true );

$ViewList = array();

$ViewList['upload'] = array(
    'functions' => array( 'upload' ),
    'script' => 'upload.php',
    'params' => array( 'ObjectID', 'ObjectVersion' )
);

$ViewList['delete'] = array(
    'script' => 'delete.php',
    'functions' => array( 'delete' ),
    'params' => array( 'objectID')
);

$FunctionList = array();
$FunctionList['upload'] = array();
$FunctionList['delete'] = array();