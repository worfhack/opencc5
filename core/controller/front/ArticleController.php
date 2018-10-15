<?php

class ArticleController extends FrontController
{
    
    public function index($id)
    {

           $article = new Article($id);
            if (!$article->id)
            {
                throw new NotFoundException();
            }
            $this->viewManager->initVariable(

                    array('title'=>$article->getTitle(),
                        'content'=>$article->getContent(),
                        'dateAdd'=>$article->getDateAdd(),
                        'name'=>$article->getName(),
                        'author'=>$article->getAuthor(),
            ));

            echo $this->viewManager->render("pages/article.html");
         /*   echo $this->twig->render("pages/$page.html", array(
                'code_base_url' => "/"._ISO_LANG_."/$code/",
                'page' => $page,
                'code' => $code,
                'iso_lang' => _ISO_LANG_,
                'GOOGLE_MAPS_API_KEY' => GOOGLE_MAPS_API_KEY,
                'OPENWEATHERMAP_API_KEY' => OPENWEATHERMAP_API_KEY,
                'thumb' => $thumb,
                'activity_json' => json_encode($detail),
                'activity_feed_json' => json_encode($feeds),
                'activity_feed' => $feeds,
                'customer_detail_json' => json_encode([
                    'firstname' => $customer_detail['firstname'],
                    'lastname' => $customer_detail['lastname'],
                 ]),

                'activity_name' => $product->name,
                'activity_subtitle' => $product->subtitle,
		// FPTP-156
                'activity_reference' => $detail->tour_ref_uniq,
		'order_reference' => $order->reference,	
		// fin FPTP-156
		'activity_temperature' => ' ', // no break space
                'activity_location' => 'Paris, France',
                'activity_date' => $date->format(_DATE_FORMAT_),
                'activity_quantity_adult' =>$detail->quantity_adult,
                'activity_quantity_child' => $detail->quantity_child,
                'activity_meeting_point_address' => $detail->meeting_point_address,
                'activity_meeting_point_latitude' => $detail->meeting_point_latitude,
                'activity_meeting_point_longitude' => $detail->meeting_point_longitude,
                'activity_start_hour' => $date->format(_HOUR_FORMAT_),
                'from_now_label' => '...',
                'customer_detail' => $customer_detail,
            ));
            return 200;
        } else {
            echo $this->twig->render('pages/404.html', array());
            return 404;
        }*/


    }

}
