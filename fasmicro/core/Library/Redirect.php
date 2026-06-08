<?php

namespace Core\Library;

class Redirect
{
    static public function page($caminho, $widt = [])
    {
        $hasValidationErrors = Session::get('formErrors') !== false;
        
        foreach ($widt as $key => $value) {
        
            if ($key === 'msgError' && $hasValidationErrors) {
                continue;
            }

            Session::set($key, $value);
        }

        header("Location: " . baseUrl() . $caminho);
        exit;
    }
}