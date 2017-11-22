<?php

class Read  {

    function topicRead($topicId,$userId)
    {
        /*查找article_read表中，该文章是否存在该user，存在则什么都不干，否则read_sum +1*/
        $finder = ['conditions'=>"article_id =".$topicId." and uid = "$userId];
        $readData   =   ArticleRead::findFirst( $finder )->toArray() ;

        if(!$readData)
        {
            $userRead   =   new ArticleRead();
            $userRead->article_id   =   $topicId;
            $userRead->article_read =   $userRead->article_read + 1;
            $userRead->dates        =   time();
            $userRead->uid          =   $userId;
             $userRead->save();

            $issued = Issued::findFirst($topicId);
            $issued->read_sum   =   $issued->read_sum + 1;
            $issued->save(); 

            $readData = $issued->toArray();
            // $readCount = $readData[0]['read_sum'];
        }

        // $readCount = $readData[0]['read_sum'];

        return $readData;
            
    }

}