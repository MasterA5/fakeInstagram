<?php
// TODO: Agregar capacidad de generar uuid4/uuid5 -> esto para generar id aleatorio

// NOTE: stackoverflow deberia ser considerado como la octava maravilla del mundo

function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); 
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); 
    return (string)vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
?>