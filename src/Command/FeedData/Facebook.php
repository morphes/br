<?php

namespace App\Command\FeedData;

class Facebook extends FeedDataAbstract
{
    public function getData()
    {
        $data = parent::getData();
        if(isset($data['link'])) {
            $data['link'] .= '?utm_source=facebook&utm_medium=cpc&utm_campaign=remarketing_facebook';
        }
        return $data;
    }
}