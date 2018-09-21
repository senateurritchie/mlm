<?php
namespace AppBundle\Extensions\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension{

    
    public function getFilters(){
        return array(
            new TwigFilter('generateToken', array($this, 'generateTokenFilter')),
            new TwigFilter('dateDiff', array($this, 'dateDiffFilter')),
            new TwigFilter('truncate', array($this, 'truncateFilter')),
            new TwigFilter('basename', array($this, 'basenameFilter')),
        );
    }



    public function generateTokenFilter($length = 8){
        return substr(trim(base64_encode(bin2hex(openssl_random_pseudo_bytes(64,$ok))),"="),0,$length);
    }

    public function dateDiffFilter($start,$end){
        return $start->diff($end);
    }

    public function truncateFilter($value,$length,$overflow="..."){
        $text = substr($value,0,$length);

        return strlen($value) > $length ? $text.$overflow : $text;
    }

    public function basenameFilter($value){
        return basename($value);
    }
}