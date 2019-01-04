<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminMediaController extends AdminController
{
    public function list()
    {

        $collectionManager = new MediaCollection($this->id_lang);
        $collectionManager->load();


        $this->viewManager->initVariable(

            array('medias' => $collectionManager)
        );
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        {
            $format = Tools::getValue('format', 'json');
            switch ($format)
            {
                case 'tiny':
                    header('Content-type: text/json');
                    echo $this->viewManager->render('pages/media/listTiny.html', [
                    ]);
                    break;
                case "json":
                default:
                 die(json_encode( $collectionManager->toArrayJSON()));



            }
         
        
            //CODE HERE
        }else
        {
           
            echo $this->viewManager->render('pages/media/list.html', [
            ]);
        }

    }

    public function form($id_media = false)
    {

        if ($id_media !== false) {
            $media = new Media($id_media, _ID_LANG_);
        } else {
            $media = NULL;
        }
        $this->viewManager->initVariable(
            [
                'media' => $media,
            ]);


        echo $this->viewManager->render('pages/media/form.html', [
        ]);

    }

    public function add()
    {


// Check if image file is a actual image or fake image
        if (isset($_FILES["file"])) {
            $target_dir = MEDIA_DIR;
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["file"]["tmp_name"]);

            if ($check !== false) {

                if (!file_exists($target_file)) {


                    if (!($_FILES["file"]["size"] > 5000000)) {

                        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg"
                            || $imageFileType == "gif"
                        ) {

                            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                                $media = new Media();
                                $media->name = basename($_FILES["file"]["name"]);
                                $media->add();
                                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                                {

                                    die(json_encode(array('name'=>$media->name, "id"=>$media->id)));
                                    //CODE HERE
                                }
                                else{
                                    Tools::redirectAdmin('/media');
                                }
                            }else
                            {
                                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                                {

                                    die(json_encode(array('error'=>true)));
                                    //CODE HERE
                                }
                                else{
                                    Tools::redirectAdmin('/media');
                                }
                            }
                        }
                    }
                }
            }

        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        {

            die(json_encode(array('error'=>true)));
            //CODE HERE
        }
        else{
            Tools::redirectAdmin('/media');
        }

    }


    public function remove($id)
    {
        $media = new Configuration($id);
        $media->delete();
        Tools::redirectAdmin('/media');
    }

}
