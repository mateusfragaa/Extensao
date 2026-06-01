<?php
if (!function_exists('teste')) {
    function teste(): string
    {
        $html = '';
        
        $html .= <<<'JS'
        <script>
            console.log('teste');
        
        </script>
        
        JS;
        
        return $html;
    }
}