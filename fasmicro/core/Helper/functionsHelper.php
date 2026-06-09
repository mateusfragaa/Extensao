<?php
/** 
 * Função para destruir variáveis já usadas
*/
function destroy(&$var){
    unset($var);
}