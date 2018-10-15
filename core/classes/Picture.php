<?php

    class Picture
    {


        //Resize des images.
       static  public function resize ($sourceFilePath , $dest_path ,  $width , $height , $suffix = false , $format = 'JPG' , $resize_type = 'resize' , $suffix_separateur = '-')
        {

            $thumb = PhpThumbFactory::create ($sourceFilePath);




            switch ($resize_type)
            {
                case 'resize' :
                    $thumb->resize ($width , $height);
                    break;
                case 'cropFromCenter' :
                    $thumb->cropFromCenter ($width , $height);
                    break;
                case 'adaptiveResize' :
                    $thumb->adaptiveResize ($width , $height);
                    break;
                case 'resize_proportionale' :
                    $thumb->resize_proportionale ($width , $height);
                    break;
                default :
                    $thumb->resize ($width , $height);
                    break;
            }
            $thumb->format = strtoupper ($format);

            $destinationFilePath = $dest_path ;
            $thumb->save ($destinationFilePath);
            return true;
        }


    }

?>
