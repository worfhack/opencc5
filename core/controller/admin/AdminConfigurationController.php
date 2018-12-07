<?php

/**
 * Created by PhpStorm.
 * User: defiant
 * Date: 25/10/2018
 * Time: 16:31
 */
class AdminConfigurationController extends AdminController
{
    public function list()
    {

        $collectionManager = new ConfigurationCollection($this->id_lang);
        $collectionManager->load();

        $this->viewManager->initVariable(

            array('configurations' => $collectionManager)
        );
        echo $this->viewManager->render('pages/configuration/list.html', [
        ]);
    }

    public function form($id_configuration = false)
    {

        if ($id_configuration != false)
        {
            $configuration = new Configuration($id_configuration, _ID_LANG_ );
        }else
        {
            $configuration =  NULL;
        }
        $this->viewManager->initVariable(
            [
                'configuration'=>$configuration,
            ]);


        echo $this->viewManager->render('pages/configuration/form.html', [
        ]);

    }

    public function add()
    {
        $configuration = new Configuration();
        $configuration->copy_from_post();
        $configuration->add();
        Tools::redirectAdmin('/configuration');

    }

    public function remove($id)
    {
        $configuration = new Configuration($id);
        $configuration->delete();
        Tools::redirectAdmin('/configuration');
    }
    public function edit($id)
    {


        $configuration = new Configuration($id, _ID_LANG_);
        $configuration->copy_from_post();
        $configuration->update();

        Tools::redirectAdmin('/configuration');

    }
}