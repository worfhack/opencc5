<?php



function p($var='')
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function d($var='')
{

    die(p($var));
}
function pSQL ($string , $htmlOK = false , $strip = true)
{
    if ( is_array ($string) ) {
        return $string;
    }
    if ( !is_numeric ($string) ) {
        if ( get_magic_quotes_gpc () ) {
            $string = stripslashes ($string);
        }
        $string = mysqli_real_escape_string (Db::getInstance()->getLink(), $string);
    }
    return $string;
}

//function sendMail($content)